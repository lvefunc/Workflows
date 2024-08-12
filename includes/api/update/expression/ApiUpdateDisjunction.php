<?php

namespace Workflows\Api\Update\Expression;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Expression\Disjunction;
use Workflows\Expression\Expression;

final class ApiUpdateDisjunction extends ApiUpdateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $disjunction = $unitOfWork->findByID( Disjunction::class, $params["id"] );

        if ( isset( $params["left_expr_id"] ) ) {
            $leftExpression = $unitOfWork->findByID( Expression::class, $params["left_expr_id"] );
            $disjunction->setLeft( $leftExpression );
        }

        if ( isset( $params["right_expr_id"] ) ) {
            $rightExpression = $unitOfWork->findByID( Expression::class, $params["right_expr_id"] );
            $disjunction->setRight( $rightExpression );
        }

        $unitOfWork->commit();

        Utils::setResult( $this, $disjunction );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "left_expr_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "right_expr_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ]
        ];
    }
}