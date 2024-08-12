<?php

namespace Workflows\Api\Create\Expression;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Enumeration\RuntimeUserExpressionType;
use Workflows\Expression\RuntimeUserExpression;

final class ApiCreateRuntimeUserExpression extends ApiCreateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $runtimeUserExpression = new RuntimeUserExpression( $params["type"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $runtimeUserExpression );
    }

    public function getAllowedParams() : array {
        return [
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