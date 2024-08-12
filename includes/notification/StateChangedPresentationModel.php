<?php

namespace Workflows\Notification;

use EchoEventPresentationModel;
use Message;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Title;
use Workflows\Enumeration\ExecutionState;
use Workflows\Runtime\RtWorkflow;

final class StateChangedPresentationModel extends EchoEventPresentationModel {
    /**
     * @inheritDoc
     */
    public function getIconType() : string {
        return "workflows-state-changed";
    }

    /**
     * @throws ReflectionException
     * @throws MWException
     */
    public function getHeaderMessage() : Message {
        if ( $this->isBundled() ) {
            $message = $this->msg( "workflows-notification-state-changed-bundled" );
            $message->params( $this->getBundleCount() );
        } else {
            $rtWorkflow = UnitOfWork::getInstance()->findByID( RtWorkflow::class, $this->event->getExtraParam( "id" ) );

            $message = $this->msg( "workflows-notification-state-changed" );
            $message->params( $rtWorkflow->getName() );
            $message->params( $this->getLocalisedExecutionState( $rtWorkflow->getState()->getExecutionState() )->text() );
        }

        return $message;
    }

    /**
     * @throws MWException
     */
    public function getLocalisedExecutionState( int $execState ) : Message {
        switch ( $execState ) {
            case ExecutionState::NotStarted:
                return $this->msg( "workflows-model-state-not-started" );
            case ExecutionState::InProgress:
                return $this->msg( "workflows-model-state-in-progress" );
            case ExecutionState::Completed:
                return $this->msg( "workflows-model-state-completed" );
            case ExecutionState::Skipped:
                return $this->msg( "workflows-model-state-skipped" );
            case ExecutionState::Obsolete:
                return $this->msg( "workflows-model-state-obsolete" );
            default:
                throw new MWException( "Unsupported execution state" );
        }
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryLink() {
        return $this->getPageLink( Title::newFromText( "Special:Workflows" ), "", true );
    }
}