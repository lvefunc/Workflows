<?php

namespace Workflows\Api\Update\Definition\Element;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Element\Gateway\InclusiveGateway;
use Workflows\Definition\Workflow;
use Workflows\Enumeration\GatewayDirection;

final class ApiUpdateInclusiveGateway extends ApiUpdateElementBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $inclusiveGateway = $unitOfWork->findByID( InclusiveGateway::class, $params["id"] );

        if ( isset( $params["name"] ) ) {
            $inclusiveGateway->setWord( $params["name"] );
        }

        if ( isset( $params["workflow_id"] ) ) {
            $workflow = $unitOfWork->findByID( Workflow::class, $params["workflow_id"] );
            $workflow->addElement( $inclusiveGateway );
        }

        if ( isset( $params["direction"] ) ) {
            $inclusiveGateway->setDirection( GatewayDirection::valueOf( $params["direction"] ) );
        }

        $unitOfWork->commit();

        Utils::setResult( $this, $inclusiveGateway );
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
            "direction" => [
                ParamValidator::PARAM_TYPE => [
                    GatewayDirection::toString( GatewayDirection::Diverging ),
                    GatewayDirection::toString( GatewayDirection::Converging )
                ],
                ParamValidator::PARAM_REQUIRED => false
            ]
        ];
    }
}