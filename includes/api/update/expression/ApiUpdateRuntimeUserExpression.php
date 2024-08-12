<?php

namespace Workflows\Api\Update\Expression;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Enumeration\RuntimeUserExpressionType;
use Workflows\Expression\RuntimeUserExpression;

final class ApiUpdateRuntimeUserExpression extends ApiUpdateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $runtimeUserExpression = $unitOfWork->findByID( RuntimeUserExpression::class, $params["id"] );
        $runtimeUserExpression->setType( $params["type"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $runtimeUserExpression );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "type" => [
                ParamValidator::PARAM_TYPE => [
                    RuntimeUserExpressionType::Owner,
                    RuntimeUserExpressionType::Random
                ],
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}