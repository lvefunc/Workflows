<?php

namespace Workflows\Api;

use ApiBase;
use ApiUsageException;
use MiniORM\Entity;
use MiniORM\Expression\Condition;
use MiniORM\Expression\Conjunction;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Workflows\Runtime\Task\RtTask;

final class Utils {
    /**
     * @throws MWException
     * @throws ReflectionException
     */
    public static function setResult( ApiBase $module, Entity $entity ) {
        $module->getResult()->addValue( null, "result", $entity->serialize() );
    }

    /**
     * @throws MWException
     * @throws ReflectionException
     */
    public static function appendResult( ApiBase $module, Entity $entity ) {
        $module->getResult()->addValue( "result", null, $entity->serialize() );
    }

    /**
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public static function read( ApiBase $module, $className, $conditions = [] ) {
        $params = $module->extractRequestParams();

        if ( isset( $params["id"] ) ) {
            $entity = UnitOfWork::getInstance()->findByID( $className, $params["id"] );
            self::setResult( $module, $entity );

            return;
        }

        $from = $params["from"] ?? 0;
        $limit = $params["limit"] ?? 10;

        $conjunction = new Conjunction();
        $conjunction->add( new Condition( "id", Condition::MoreThan, $from ) );

        foreach ( $conditions as $condition ) {
            $conjunction->add( $condition );
        }

        $entities = UnitOfWork::getInstance()->findMultiple( $className, $conjunction, ( $limit + 1 ) );

        for ( $i = 0; $i < ( $limit + 1 ); $i++ ) {
            if ( !isset( $entities[$i] ) ) {
                return;
            }

            if ( $i === $limit ) {
                $module->getContinuationManager()->addContinueParam( $module, "from", $entities[$limit - 1]->getID() );

                return;
            }

            self::appendResult( $module, $entities[$i] );
        }
    }
}