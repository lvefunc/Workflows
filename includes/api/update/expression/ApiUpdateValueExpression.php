<?php

namespace Workflows\Api\Update\Expression;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Expression\ValueExpression;
use Workflows\Value\Value;

final class ApiUpdateValueExpression extends ApiUpdateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $value = $unitOfWork->findByID( Value::class, $params["value_id"] );
        $valueExpression = $unitOfWork->findByID( ValueExpression::class, $params["id"] );
        $valueExpression->setValue( $value );
        $unitOfWork->commit();

        Utils::setResult( $this, $valueExpression );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "value_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}