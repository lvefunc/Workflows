<?php

namespace Workflows\Notification;

use EchoEventPresentationModel;
use Message;
use Title;

final class NewTaskPresentationModel extends EchoEventPresentationModel {
    /**
     * @inheritDoc
     */
    public function getIconType() : string {
        return "workflows-new-task";
    }

    public function getHeaderMessage() : Message {
        if ( $this->isBundled() ) {
            $message = $this->msg( "workflows-notification-new-task-bundled" );
            $message->params( $this->getBundleCount() );
        } else {
            $message = $this->msg( "workflows-notification-new-task" );
        }

        return $message;
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryLink() {
        return $this->getPageLink( Title::newFromText( "Special:Workflows" ), "", true );
    }
}