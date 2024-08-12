<?php

namespace Workflows\Api\Create\Definition;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\RequiredInput;
use Workflows\Definition\Workflow;
use Workflows\Enumeration\ValueType;

final class ApiCreateRequiredInput extends ApiCreateDefinitionBase {
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

        $requiredInput = new RequiredInput( $params["name"], $params["type"] );

        $workflow = $unitOfWork->findByID( Workflow::class, $params["workflow_id"] );
        $workflow->addRequiredInput( $requiredInput );

        $unitOfWork->commit();
        $this->getResult()->addValue( null, "result", $requiredInput->serialize() );
    }

    public function getAllowedParams() : array {
        return [
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "type" => [
                ParamValidator::PARAM_TYPE => [
                    ValueType::Boolean,
                    ValueType::Integer,
                    ValueType::Text
                ],
                ParamValidator::PARAM_REQUIRED => true,
                ParamValidator::PARAM_DEFAULT => ValueType::Boolean
            ],
            "workflow_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}