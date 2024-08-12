<?php

namespace Workflows\Api\Create\Expression;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Expression\Conjunction;
use Workflows\Expression\Expression;

final class ApiCreateConjunction extends ApiCreateExpressionBase {
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

        $leftExpression = $unitOfWork->findByID( Expression::class, $params["left_expr_id"] );
        $rightExpression = $unitOfWork->findByID( Expression::class, $params["right_expr_id"] );
        $conjunction = new Conjunction( $leftExpression, $rightExpression );
        $unitOfWork->commit();

        Utils::setResult( $this, $conjunction );
    }

    public function getAllowedParams() : array {
        return [
            "left_expr_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "right_expr_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}