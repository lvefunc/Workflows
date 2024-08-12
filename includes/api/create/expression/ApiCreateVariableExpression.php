<?php

namespace Workflows\Api\Create\Expression;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Expression\VariableExpression;

final class ApiCreateVariableExpression extends ApiCreateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $variableExpression = new VariableExpression( $params["variable_name"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $variableExpression );
    }

    public function getAllowedParams() : array {
        return [
            "variable_name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}