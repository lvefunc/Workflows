<?php

namespace Workflows\Runtime\Element\Gateway;

use MWException;
use Workflows\Enumeration\ExecutionState;
use Workflows\Enumeration\GatewayDirection;

/**
 * @Table(name: "wfs_rt_element_exclusive_gateway")
 * @BaseEntity(name: "Workflows\Runtime\Element\Gateway\RtGateway")
 */
final class RtExclusiveGateway extends RtGateway {
    /**
     * @throws MWException
     */
    public function queue() : void {
        switch ( $this->getDirection() ) {
            case GatewayDirection::Diverging:
                $preceding = $this->getWorkflow()->findPrecedingElementOf( $this );

                if ( $preceding->getState()->isCompleted() ) {
                    $this->getState()->setExecutionState( ExecutionState::InProgress );
                    $this->getWorkflow()->continueExecution();
                }

                break;
            case GatewayDirection::Converging:
                $preceding = $this->getWorkflow()->findPrecedingElementsOf( $this );

                foreach ( $preceding as $element ) {
                    if ( $element->getState()->isCompleted() ) {
                        $this->getState()->setExecutionState( ExecutionState::InProgress );
                        $this->getWorkflow()->continueExecution();
                        break;
                    }
                }

                break;
        }
    }

    /**
     * @throws MWException
     */
    public function end() : void {
        switch ( $this->getDirection() ) {
            case GatewayDirection::Diverging:
                $outgoing = $this->getWorkflow()->findOutgoingTransitions( $this );
                $context = $this->getWorkflow()->getContext();

                $default = null;

                foreach ( $outgoing as $transition ) {
                    if ( is_null( $transition->getLogicalExpression() ) ) {
                        $default = $transition;
                    } else {
                        if ( $transition->getLogicalExpression()->evaluate( $context ) ) {
                            $this->getState()->setExecutionState( ExecutionState::Completed );

                            $succeeding = $transition->getTarget();
                            $token = $context->findTokenByPosition( $this );
                            $token->moveTo( $succeeding );
                            $succeeding->queue();

                            return;
                        }
                    }
                }

                if ( is_null( $default ) ) {
                    throw new MWException(
                        "Cannot continue workflow execution, no logical expressions contained in outgoing " .
                        "transitions of exclusive diverging gateway got evaluated as true and default " .
                        "transition was not found. Workflow is probably malformed."
                    );
                }


                $this->getState()->setExecutionState( ExecutionState::Completed );

                $succeeding = $default->getTarget();
                $token = $this->getWorkflow()->getContext()->findTokenByPosition( $this );
                $token->moveTo( $succeeding );
                $succeeding->queue();

                break;
            case GatewayDirection::Converging:
                $this->getState()->setExecutionState( ExecutionState::Completed );

                $succeeding = $this->getWorkflow()->findElementSucceedingTo( $this );
                $token = $this->getWorkflow()->getContext()->findTokenByPosition( $this );
                $token->moveTo( $succeeding );
                $succeeding->queue();

                break;
        }
    }
}