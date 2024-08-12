<?php

namespace Workflows\Runtime;

use EchoEvent;
use Exception;
use MiniORM\Entity;
use MiniORM\Expression\Condition;
use MiniORM\UnitOfWork;
use MWException;
use RequestContext;
use User;
use Workflows\Definition\Workflow;
use Workflows\Enumeration\EventType;
use Workflows\Enumeration\ExecutionState;
use Workflows\Runtime\Context\Context;
use Workflows\Runtime\Context\Token;
use Workflows\Runtime\Element\Activity\RtUserActivity;
use Workflows\Runtime\Element\RtElement;
use Workflows\Runtime\Element\RtEvent;
use Workflows\Runtime\Transition\RtTransition;

/**
 * @Table(name: "wfs_rt_workflow")
 */
final class RtWorkflow extends Entity {
    /**
     * @Column(name: "prototype_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Definition\Workflow")
     */
    private Workflow $prototype;

    /**
     * @Column(name: "name", type: "varbinary", length: 255, nullable: false)
     */
    private string $name;

    /**
     * @Column(name: "owner", type: "int", nullable: false)
     */
    private User $owner;

    /**
     * @Column(name: "state_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Runtime\RtState")
     */
    private RtState $state;

    /**
     * @OneToOne(target: "Workflows\Runtime\Context\Context", mappedBy: "workflow")
     */
    private Context $context;

    /**
     * @var RtElement[]
     * @OneToMany(target: "Workflows\Runtime\Element\RtElement", mappedBy: "workflow")
     */
    private array $elements = [];

    /**
     * @var RtTransition[]
     * @OneToMany(target: "Workflows\Runtime\Transition\RtTransition", mappedBy: "workflow")
     */
    private array $transitions = [];

    public function __construct( Workflow $prototype, array $inputs = [] ) {
        parent::__construct();
        $this->prototype = $prototype;

        // Find out how many instances of given prototype there is
        $condition = new Condition( "prototype_id", Condition::EqualTo, $prototype->getID() );
        $count = UnitOfWork::getInstance()->count( RtWorkflow::class, $condition );

        // And set name of this instance as prototype name followed by the number
        // of already existing instances of such prototype incremented by one
        $this->name = $prototype->getName() . " #" . ++$count;

        $this->owner = RequestContext::getMain()->getUser();
        $this->state = new RtState();
        $this->context = new Context( $this, $inputs );

        foreach ( $prototype->getRequiredInputs() as $requiredInput ) {
            $this->getContext()->verifyRequiredInput( $requiredInput );
        }

        $elementMapping = [];

        foreach ( $prototype->getElements() as $element ) {
            $rtElement = $element->createRuntimeInstance();
            $rtElement->setWorkflow( $this );
            $this->elements[] = $rtElement;
            $elementMapping[$element->getHash()] = $rtElement;
        }

        foreach ( $prototype->getTransitions() as $transition ) {
            $source = $elementMapping[$transition->getSource()->getHash()];
            $target = $elementMapping[$transition->getTarget()->getHash()];
            $rtTransition = new RtTransition( $source, $target, $transition->getLogicalExpression(), $this );
            $this->transitions[] = $rtTransition;
        }
    }

    /**
     * Start workflow execution. Marks this workflow as being in progress, creates a token at the beginning
     * of this workflow and queues the first element. If this workflow is in progress or was completed then
     * exception will get thrown.
     *
     * @throws MWException
     * @throws Exception
     */
    public function startExecution() : void {
        switch ( $this->getState()->getExecutionState() ) {
            case ExecutionState::NotStarted:
                $this->getState()->setExecutionState( ExecutionState::InProgress );

                EchoEvent::create( [
                    "type" => "workflows-state-changed",
                    "extra" => [
                        "id" => $this->getID()
                    ]
                ] );

                foreach ( $this->getElements() as $element ) {
                    if (
                        $element instanceof RtEvent &&
                        $element->getType() == EventType::Start
                    ) {
                        $token = new Token( $element );
                        $this->getContext()->addToken( $token );

                        $element->queue();
                    }
                }

                break;
            case ExecutionState::InProgress:
                throw new MWException( "Workflow \"" . $this->name .  "\" is already running" );
            case ExecutionState::Completed:
                throw new MWException( "Workflow \"" . $this->name . "\" was already completed" );
        }
    }

    /**
     * Continue workflow execution by ending all queued elements that don't require human interaction.
     * Should be called at the end of each RtElement::queue() function.
     *
     * @throws MWException
     */
    public function continueExecution() : void {
        $elements = $this->findElementsByState( ExecutionState::InProgress );

        foreach ( $elements as $element ) {
            if ( !( $element instanceof RtUserActivity ) ) {
                $element->end();
            }
        }
    }

    public function getPrototype() : Workflow {
        return $this->prototype;
    }

    public function getName() : string {
        return $this->name;
    }

    public function getOwner() : User {
        return $this->owner;
    }

    public function getState() : RtState {
        return $this->state;
    }

    public function getContext() : Context {
        return $this->context;
    }

    /**
     * Get all elements in this workflow
     *
     * @return RtElement[] Elements
     */
    public function getElements() : array {
        return $this->elements;
    }

    /**
     * Get all transitions in this workflow
     *
     * @return RtTransition[] Transitions
     */
    public function getTransitions() : array {
        return $this->transitions;
    }

    /**
     * Find all elements in this workflow that are in the given execution state.
     *
     * @param int $executionState Execution state to filter elements by
     *
     * @return RtElement[] Elements filtered by execution state
     * @throws MWException In case value passed is not a valid execution state
     */
    public function findElementsByState( int $executionState ) : array {
        ExecutionState::verify( $executionState );
        $elements = [];

        foreach ( $this->elements as $element ) {
            if (
                $executionState === $element->getState()->getExecutionState() &&
                !in_array( $element, $elements )
            ) {
                $elements[] = $element;
            }
        }

        return $elements;
    }

    /**
     * Find all elements in this workflow that precede element passed to this function.
     *
     * @param RtElement $element Element to find preceding of
     *
     * @return RtElement[] Preceding elements
     */
    public function findPrecedingElementsOf( RtElement $element ) : array {
        $elements = [];

        foreach ( $this->transitions as $transition ) {
            if (
                $element->equals( $transition->getTarget() ) &&
                !in_array( $transition->getSource(), $elements )
            ) {
                $elements[] = $transition->getSource();
            }
        }

        return $elements;
    }

    /**
     * Find element in this workflow that precedes element passed to this function.
     * In case there are multiple preceding elements only the first one is returned.
     *
     * @param RtElement $element Element to find preceding of
     *
     * @return RtElement Preceding element
     * @throws MWException Thrown in case none found
     */
    public function findPrecedingElementOf( RtElement $element ) : RtElement {
        $elements = $this->findPrecedingElementsOf( $element );

        if ( count( $elements ) < 1 ) {
            throw new MWException(
                "Expected to find at least one preceding element " .
                "of element with ID = \"" . $element->getID() . "\" but found none"
            );
        }

        return $elements[0];
    }

    /**
     * Find all elements in this workflow that are succeeding to element passed to this function.
     *
     * @param RtElement $element Element to find succeeding of
     *
     * @return RtElement[] Succeeding elements
     */
    public function findElementsSucceedingTo( RtElement $element ) : array {
        $elements = [];

        foreach ( $this->transitions as $transition ) {
            if (
                $element->equals( $transition->getSource() ) &&
                !in_array( $transition->getTarget(), $elements )
            ) {
                $elements[] = $transition->getTarget();
            }
        }

        return $elements;
    }

    /**
     * Find element in this workflow that is succeeding to element passed to this function.
     * In case there are multiple succeeding elements only the first one is returned.
     *
     * @param RtElement $element Element to find succeeding of
     *
     * @return RtElement Succeeding element
     * @throws MWException Thrown in case none found
     */
    public function findElementSucceedingTo( RtElement $element ) : RtElement {
        $elements = $this->findElementsSucceedingTo( $element );

        if ( count( $elements ) < 1 ) {
            throw new MWException(
                "Expected to find at least one element succeeding to " .
                "element with ID = \"" . $element->getID() . "\" but found none"
            );
        }

        return $elements[0];
    }

    /**
     * Find all transitions that are ingoing to element passed to this function.
     *
     * @param RtElement $element Element to find ingoing transitions of
     *
     * @return RtTransition[] Ingoing transitions
     */
    public function findIngoingTransitions( RtElement $element ) : array {
        $transitions = [];

        foreach ( $this->transitions as $transition ) {
            if (
                $element->equals( $transition->getTarget() ) &&
                !in_array( $transition, $transitions )
            ) {
                $transitions[] = $transition;
            }
        }

        return $transitions;
    }

    /**
     * Find all transitions that are outgoing to element passed to this function.
     *
     * @param RtElement $element Element to find outgoing transitions of
     *
     * @return RtTransition[] Outgoing transitions
     */
    public function findOutgoingTransitions( RtElement $element ) : array {
        $transitions = [];

        foreach ( $this->transitions as $transition ) {
            if (
                $element->equals( $transition->getSource() ) &&
                !in_array( $transition, $transitions )
            ) {
                $transitions[] = $transition;
            }
        }

        return $transitions;
    }
}