<?php

namespace Workflows;

use EchoEvent;
use MediaWiki\MediaWikiServices;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Workflows\Runtime\RtWorkflow;
use Workflows\Runtime\Task\RtTask;

final class Utils {
    private function __construct() {
    }

    /**
     * @throws MWException
     * @throws ReflectionException
     */
    public static function locateUsers( EchoEvent $echoEvent ) : array {
        $unitOfWork = UnitOfWork::getInstance();

        switch ( $echoEvent->getType() ) {
            case "workflows-new-task":
                return [
                    MediaWikiServices::getInstance()->getUserFactory()->newFromId( $echoEvent->getExtraParam( "assignee" ) )
                ];
            case "workflows-state-changed":
                $rtWorkflow = $unitOfWork->findByID( RtWorkflow::class, $echoEvent->getExtraParam( "id" ) );

                return [ $rtWorkflow->getOwner() ];
            default:
                throw new MWException( "Unsupported event type" );
        }
    }
}