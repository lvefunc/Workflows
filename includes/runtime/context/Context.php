<?php

namespace Workflows\Runtime\Context;

use Exception;
use MiniORM\Entity;
use MWException;
use Workflows\Definition\RequiredInput;
use Workflows\Enumeration\ValueType;
use Workflows\Runtime\Element\RtElement;
use Workflows\Runtime\RtWorkflow;
use Workflows\Value\Value;

/**
 * @Table(name: "wfs_rt_context")
 */
final class Context extends Entity {
    /**
     * @Column(name: "workflow_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Runtime\RtWorkflow")
     */
    private RtWorkflow $workflow;

    /**
     * @var Variable[]
     * @OneToMany(target: "Workflows\Runtime\Context\Variable", mappedBy: "context")
     */
    private array $variables = [];

    /**
     * @var Token[]
     * @OneToMany(target: "Workflows\Runtime\Context\Token", mappedBy: "context")
     */
    private array $tokens = [];

    public function __construct( RtWorkflow $workflow, array $inputs = [] ) {
        parent::__construct();
        $this->workflow = $workflow;

        foreach ( $inputs as $input ) {
            $this->createVariable( $input->getName(), $input->getValue() );
        }
    }

    /**
     * @throws MWException
     */
    public function verifyRequiredInput( RequiredInput $requiredInput ) {
        foreach ( $this->variables as $variable ) {
            if ( $requiredInput->getName() !== $variable->getName() ) {
                continue;
            }

            $value = $variable->getValue();
            $type = ValueType::typeOf( $value );

            if ( $requiredInput->getType() !== $type ) {
                throw new MWException(
                    "Type mismatch, expected variable by name \"" . $requiredInput->getName() . "\" " .
                    "to be of \"" . $requiredInput->getType() . "\", but it was of \"" . $type . "\" type"
                );
            }

            return;
        }

        throw new MWException( "Found no variable by name \"" . $requiredInput->getName() . "\"" );
    }

    /**
     * Returns whether this context contains variable with given name
     *
     * @param string $name Name to find variable by
     *
     * @return bool Whether this context contains such variable
     */
    public function containsVariable( string $name ) : bool {
        foreach ( $this->variables as $variable ) {
            if ( $name === $variable->getName() ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Finds variable in this context that has the same name as the one passed to this function and returns its value.
     * Returns null if this context has no variable with such name.
     *
     * @param string $name Name to find variable by
     *
     * @return Value|null Variable value or null if there's no variable by given name
     */
    public function getVariableValue( string $name ) : ?Value {
        foreach ( $this->variables as $variable ) {
            if ( $name === $variable->getName() ) {
                return $variable->getValue();
            }
        }

        return null;
    }

    /**
     * Creates a variable in this context with given name and value. If this context already has a variable
     * with the same name as the one passed to this function then value of this variable is changed.
     *
     * @throws Exception Thrown in case unable to instantiate a variable
     */
    public function createVariable( string $name, Value $value ) : void {
        foreach ( $this->variables as $variable ) {
            if ( $name === $variable->getName() ) {
                $variable->getValue()->markAsRemoved();
                $variable->setValue( $value );

                return;
            }
        }

        $variable = new Variable( $name, $value, $this );
        $this->variables[] = $variable;
    }

    /**
     * Get all tokens in this context
     *
     * @return Token[] Tokens
     */
    public function getTokens() : array {
        return $this->tokens;
    }

    /**
     * Finds all tokens in this context that have element passed to this function as their position
     *
     * @return Token[] Tokens filtered by their position
     */
    public function findTokensByPosition( RtElement $position ) : array {
        $tokens = [];

        foreach ( $this->tokens as $token ) {
            if (
                $position->equals( $token->getPosition() ) &&
                !in_array( $token, $tokens )
            ) {
                $tokens[] = $token;
            }
        }

        return $tokens;
    }

    /**
     * Find token in this context that has element passed to this function as its position. In case there are multiple
     * tokens that satisfy this condition only the first one is returned.
     *
     * @param RtElement $position
     *
     * @return Token Token that has $position as its position
     * @throws MWException In case there are no tokens
     */
    public function findTokenByPosition( RtElement $position ) : Token {
        $tokens = $this->findTokensByPosition( $position );

        if ( count( $tokens ) < 1 ) {
            throw new MWException(
                "Expected to find at least one token that has " .
                "element with ID = \"" . $position->getID() . "\" as its position"
            );
        }

        return $tokens[0];
    }

    /**
     * Add token to this context
     *
     * @param Token $token Token to be added
     */
    public function addToken( Token $token ) {
        foreach ( $this->tokens as $existing ) {
            if ( $existing->equals( $token ) ) {
                return;
            }
        }

        $token->setContext( $this );
        $this->tokens[] = $token;
    }

    public function getWorkflow() : ?RtWorkflow {
        return $this->workflow;
    }
}