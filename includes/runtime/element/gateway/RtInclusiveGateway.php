<?php

namespace Workflows\Runtime\Element\Gateway;

use Exception;
use MWException;
use Workflows\Enumeration\ExecutionState;
use Workflows\Enumeration\GatewayDirection;
use Workflows\Runtime\Context\Token;

/**
 * @Table(name: "wfs_rt_element_inclusive_gateway")
 * @BaseEntity(name: "Workflows\Runtime\Element\Gateway\RtGateway")
 */
final class RtInclusiveGateway extends RtGateway {
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
                $tokens = $this->getWorkflow()->getContext()->findTokensByPosition( $this );

                if ( empty( $tokens ) ) {
                    // No tokens arrived yet so do nothing
                    return;
                }

                $initiator = null;

                foreach ( $tokens as $token ) {
                    if ( is_null( $initiator ) ) {
                        $initiator = $token->getInitiator();

                        if ( !(
                            $initiator instanceof RtInclusiveGateway &&
                            $initiator->getDirection() === GatewayDirection::Diverging
                        ) ) {
                            throw new MWException(
                                "Cannot continue workflow execution since one of the tokens arrived in converging " .
                                "inclusive gateway has its initiator element be not diverging inclusive gateway. " .
                                "This workflow is probably malformed."
                            );
                        }
                    }

                    if ( !$initiator->equals( $token->getInitiator() ) ) {
                        throw new MWException(
                            "Cannot continue workflow execution since one of the tokens arrived in converging " .
                            "parallel gateway has its initiator element differ from initiator element of other " .
                            "tokens. This workflow is probably malformed."
                        );
                    }
                }

                $token = $this->getWorkflow()->getContext()->findTokenByPosition( $initiator );

                foreach ( $token->getChildren() as $childToken ) {
                    if ( !in_array( $childToken, $tokens ) ) {
                        // Only part of the tokens arrived so do nothing
                        return;
                    }
                }

                $this->getState()->setExecutionState( ExecutionState::InProgress );
                $this->getWorkflow()->continueExecution();

                break;
        }
    }

    /**
     * @throws MWException
     * @throws Exception
     */
    public function end() : void {
        switch ( $this->getDirection() ) {
            case GatewayDirection::Diverging:
                $this->getState()->setExecutionState( ExecutionState::Completed );

                $outgoing = $this->getWorkflow()->findOutgoingTransitions( $this );
                $context = $this->getWorkflow()->getContext();

                $succeeding = [];

                foreach ( $outgoing as $transition ) {
                    if ( $transition->getLogicalExpression()->evaluate( $context ) ) {
                        $succeeding[] = $transition->getTarget();
                        $token = new Token( $this, $context->findTokenByPosition( $this ), $context );
                        $token->moveTo( $transition->getTarget() );
                    }
                }

                foreach ( $succeeding as $element ) {
                    $element->queue();
                }

                break;
            case GatewayDirection::Converging:
                $this->getState()->setExecutionState( ExecutionState::Completed );

                $succeeding = $this->getWorkflow()->findElementSucceedingTo( $this );
                $tokens = $this->getWorkflow()->getContext()->findTokensByPosition( $this );
                $token = $tokens[0]->getParent();
                $token->moveTo( $succeeding );
                $succeeding->queue();

                break;
        }
    }
}