<?php

namespace Workflows\Api\Create\Definition\Element;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Element\Gateway\Gateway;
use Workflows\Definition\Element\Gateway\ParallelGateway;
use Workflows\Definition\Workflow;
use Workflows\Enumeration\GatewayDirection;

final class ApiCreateParallelGateway extends ApiCreateElementBase {
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
        $parallelGateway = new ParallelGateway( $params["name"], GatewayDirection::valueOf( $params["direction"] ) );
        $workflow->addElement( $parallelGateway );
        $unitOfWork->commit();

        Utils::setResult( $this, $parallelGateway );
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
            "direction" => [
                ParamValidator::PARAM_TYPE => [
                    GatewayDirection::toString( GatewayDirection::Diverging ),
                    GatewayDirection::toString( GatewayDirection::Converging )
                ],
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}