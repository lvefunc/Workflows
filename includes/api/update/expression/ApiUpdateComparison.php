<?php

namespace Workflows\Api\Update\Expression;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Enumeration\ComparisonType;
use Workflows\Expression\Comparison;
use Workflows\Expression\Expression;

final class ApiUpdateComparison extends ApiUpdateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $comparison = $unitOfWork->findByID( Comparison::class, $params["id"] );

        if ( isset( $params["left_expr_id"] ) ) {
            $leftExpression = $unitOfWork->findByID( Expression::class, $params["left_expr_id"] );
            $comparison->setLeft( $leftExpression );
        }

        if ( isset( $params["right_expr_id"] ) ) {
            $rightExpression = $unitOfWork->findByID( Expression::class, $params["right_expr_id"] );
            $comparison->setRight( $rightExpression );
        }

        if ( isset( $params["type"] ) ) {
            $comparison->setType( $params["type"] );
        }

        $unitOfWork->commit();

        Utils::setResult( $this, $comparison );
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
            ],
            "type" => [
                ParamValidator::PARAM_TYPE => [
                    ComparisonType::EqualTo,
                    ComparisonType::LessThan,
                    ComparisonType::MoreThan,
                    ComparisonType::LessThanOrEqualTo,
                    ComparisonType::MoreThanOrEqualTo
                ],
                ParamValidator::PARAM_REQUIRED => false
            ]
        ];
    }
}