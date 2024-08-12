<?php

namespace Workflows\Definition;

use MiniORM\Entity;
use MiniORM\Expression\Condition;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Workflows\Definition\Element\Element;
use Workflows\Definition\Transition\Transition;

/**
 * @Table(name: "wfs_def_workflow")
 */
final class Workflow extends Entity {
    /**
     * @Column(name: "name", type: "varbinary", length: 255, nullable: false)
     */
    private string $name;

    /**
     * @var Element[]
     * @OneToMany(target: "Workflows\Definition\Element\Element", mappedBy: "workflow")
     */
    private array $elements = [];

    /**
     * @var Transition[]
     * @OneToMany(target: "Workflows\Definition\Transition\Transition", mappedBy: "workflow")
     */
    private array $transitions = [];

    /**
     * @var RequiredInput[]
     * @OneToMany(target: "Workflows\Definition\RequiredInput", mappedBy: "workflow")
     */
    private array $requiredInputs = [];

    public function __construct( string $name ) {
        parent::__construct();
        $this->setName( $name );
    }

    public function getName() : string {
        return $this->name;
    }

    /**
     * @throws MWException
     * @throws ReflectionException
     */
    public function setName( string $name ) : void {
        $unitOfWork = UnitOfWork::getInstance();
        $condition = new Condition( "name", Condition::EqualTo, $name );
        $workflow = $unitOfWork->findSingle( Workflow::class, $condition );

        if ( !is_null( $workflow ) ) {
            throw new MWException( "Workflow name must be unique!" );
        }

        $this->name = $name;
        $this->markAsDirty();
    }

    /**
     * @return Element[]
     */
    public function getElements() : array {
        return $this->elements;
    }

    public function addElement( Element $element ) : void {
        foreach ( $this->elements as $existing ) {
            if ( $existing->equals( $element ) ) {
                return;
            }
        }

        $element->setWorkflow( $this );
        $this->elements[] = $element;
    }

    public function addElements( Element ...$elements ) : void {
        foreach ( $elements as $element ) {
            $this->addElement( $element );
        }
    }

    /**
     * @return Transition[]
     */
    public function getTransitions() : array {
        return $this->transitions;
    }

    /**
     * @throws MWException
     */
    public function addTransition( Transition $transition ) : void {
        foreach ( $this->transitions as $existing ) {
            if ( $existing->equals( $transition ) ) {
                return;
            }
        }

        if ( !$this->equals( $transition->getSource()->getWorkflow() ) ) {
            throw new MWException(
                "Transition is being added to workflow but its source element is not from this workflow"
            );
        }

        if ( !$this->equals( $transition->getTarget()->getWorkflow() ) ) {
            throw new MWException(
                "Transition is being added to workflow but its target element is not from this workflow"
            );
        }

        $transition->setWorkflow( $this );
        $this->transitions[] = $transition;
    }

    /**
     * @throws MWException
     */
    public function addTransitions( Transition ...$transitions ) : void {
        foreach ( $transitions as $transition ) {
            $this->addTransition( $transition );
        }
    }

    /**
     * Get all required inputs
     *
     * @return RequiredInput[] Required inputs
     */
    public function getRequiredInputs() : array {
        return $this->requiredInputs;
    }

    public function addRequiredInput( RequiredInput $requiredInput ) : void {
        foreach ( $this->requiredInputs as $existing ) {
            if ( $existing->equals( $requiredInput ) ) {
                return;
            }
        }

        $requiredInput->setWorkflow( $this );
        $this->requiredInputs[] = $requiredInput;
    }

    public function addRequiredInputs( RequiredInput ...$requiredInputs ) : void {
        foreach ( $requiredInputs as $requiredInput ) {
            $this->addRequiredInput( $requiredInput );
        }
    }
}