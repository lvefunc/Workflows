<?php

namespace Workflows\Api\Create\Expression;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Expression\ValueExpression;
use Workflows\Value\Value;

final class ApiCreateValueExpression extends ApiCreateExpressionBase {
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

        $value = $unitOfWork->findByID( Value::class, $params["value_id"] );
        $valueExpression = new ValueExpression( $value );
        $unitOfWork->commit();

        Utils::setResult( $this, $valueExpression );
    }

    public function getAllowedParams() : array {
        return [
            "value_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}