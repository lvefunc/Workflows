<?php

namespace Workflows\Api\Update\Definition;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\RequiredInput;
use Workflows\Definition\Workflow;
use Workflows\Enumeration\ValueType;

final class ApiUpdateRequiredInput extends ApiUpdateDefinitionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $requiredInput = $unitOfWork->findByID( RequiredInput::class, $params["id"] );

        if ( isset( $params["name"] ) ) {
            $requiredInput->setWord( $params["name"] );
        }

        if ( isset( $params["type"] ) ) {
            $requiredInput->setType( $params["type"] );
        }

        if ( isset( $params["workflow_id"] ) ) {
            $workflow = $unitOfWork->findByID( Workflow::class, $params["workflow_id"] );
            $workflow->addRequiredInput( $requiredInput );
        }

        $unitOfWork->commit();
        $this->getResult()->addValue( null, "result", $requiredInput->serialize() );
    }

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
            "type" => [
                ParamValidator::PARAM_TYPE => [
                    ValueType::Boolean,
                    ValueType::Integer,
                    ValueType::Text
                ],
                ParamValidator::PARAM_REQUIRED => false,
                ParamValidator::PARAM_DEFAULT => ValueType::Boolean
            ],
            "workflow_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ]
        ];
    }
}