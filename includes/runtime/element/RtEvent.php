<?php

namespace Workflows\Runtime\Element;

use EchoEvent;
use MWException;
use Workflows\Enumeration\EventType;
use Workflows\Enumeration\ExecutionState;

/**
 * @Table(name: "wfs_rt_element_event")
 * @BaseEntity(name: "Workflows\Runtime\Element\RtElement")
 */
final class RtEvent extends RtElement {
    /**
     * @Column(name: "type", type: "tinyint", nullable: false)
     */
    private int $type;

    public function __construct( string $name, int $type ) {
        parent::__construct( $name );
        EventType::verify( $type );
        $this->type = $type;
    }

    /**
     * @throws MWException
     */
    public function queue() : void {
        switch ( $this->getType() ) {
            case EventType::Start:
                $this->getState()->setExecutionState( ExecutionState::InProgress );
                $this->getWorkflow()->continueExecution();

                break;
            case EventType::End:
                $preceding = $this->getWorkflow()->findPrecedingElementOf( $this );

                if ( $preceding->getState()->isCompleted() ) {
                    $this->getState()->setExecutionState( ExecutionState::InProgress );
                    $this->getWorkflow()->continueExecution();
                }

                break;
        }
    }

    /**
     * @throws MWException
     */
    public function end() : void {
        switch ( $this->getType() ) {
            case EventType::Start:
                $this->getState()->setExecutionState( ExecutionState::Completed );

                $succeeding = $this->getWorkflow()->findElementSucceedingTo( $this );
                $token = $this->getWorkflow()->getContext()->findTokenByPosition( $this );
                $token->moveTo( $succeeding );
                $succeeding->queue();

                break;
            case EventType::End:
                $this->getState()->setExecutionState( ExecutionState::Completed );
                $this->getWorkflow()->getState()->setExecutionState( ExecutionState::Completed );

                EchoEvent::create( [
                    "type" => "workflows-state-changed",
                    "extra" => [
                        "id" => $this->getWorkflow()->getID()
                    ]
                ] );

                break;
        }
    }

    public function getType() : int {
        return $this->type;
    }
}