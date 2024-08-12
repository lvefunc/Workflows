<?php

namespace Workflows\Api\Update\Definition\Element;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Element\Event;
use Workflows\Definition\Workflow;
use Workflows\Enumeration\EventType;

final class ApiUpdateEvent extends ApiUpdateElementBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $event = $unitOfWork->findByID( Event::class, $params["id"] );

        if ( isset( $params["name"] ) ) {
            $event->setWord( $params["name"] );
        }

        if ( isset( $params["workflow_id"] ) ) {
            $workflow = $unitOfWork->findByID( Workflow::class, $params["workflow_id"] );
            $workflow->addElement( $event );
        }

        if ( isset( $params["type"] ) ) {
            $event->setType( EventType::valueOf( $params["type"] ) );
        }

        $unitOfWork->commit();

        Utils::setResult( $this, $event );
    }

    /**
     * @throws MWException
     */
    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "workflow_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "type" => [
                ParamValidator::PARAM_TYPE => [
                    EventType::toString( EventType::Start ),
                    EventType::toString( EventType::End )
                ],
                ParamValidator::PARAM_REQUIRED => false
            ]
        ];
    }
}