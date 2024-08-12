<?php

namespace Workflows\Api\Update\Expression;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Expression\VariableExpression;

final class ApiUpdateVariableExpression extends ApiUpdateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $variableExpression = $unitOfWork->findByID( VariableExpression::class, $params["id"] );
        $variableExpression->setVariableName( $params["variable_name"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $variableExpression );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "variable_name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}