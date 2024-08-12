<?php

namespace Workflows\Api\Create\Definition\Element;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Element\Event;
use Workflows\Definition\Workflow;
use Workflows\Enumeration\EventType;

final class ApiCreateEvent extends ApiCreateElementBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $workflow = $unitOfWork->findByID( Workflow::class, $params["workflow_id"] );
        $event = new Event( $params["name"], EventType::valueOf( $params["type"] ) );
        $workflow->addElement( $event );
        $unitOfWork->commit();

        Utils::setResult( $this, $event );
    }

    /**
     * @throws MWException
     */
    public function getAllowedParams() : array {
        return [
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "workflow_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "type" => [
                ParamValidator::PARAM_TYPE => [
                    EventType::toString( EventType::Start ),
                    EventType::toString( EventType::End )
                ],
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}