<?php

namespace Workflows\Api\Read;

use ApiUsageException;
use MiniORM\Expression\Condition;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Enumeration\Order;
use Workflows\Expression\Expression;

final class ApiReadExpression extends ApiReadBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        if ( isset( $params["id"] ) ) {
            $expression = $unitOfWork->findByID( Expression::class, $params["id"] );
            $this->getResult()->addValue( null, "result", $expression->serialize() );

            return;
        }

        $ordering = $params["ordering"] ?? Order::Ascending;
        $from = $params["from"] ?? 0;
        $limit = $params["limit"] ?? 10;

        $condition = $ordering === Order::Ascending
            ? new Condition( "id", Condition::MoreThanOrEqualTo, $from )
            : new Condition( "id", $from === 0 ? Condition::MoreThanOrEqualTo : Condition::LessThanOrEqualTo, $from );

        $options = [];
        $options["ORDER BY"] = "id " . ( $ordering === Order::Ascending ? "ASC" : "DESC" );
        $options["LIMIT"] = ( $limit + 1 );

        $expressions = $unitOfWork->findMultiple( Expression::class, $condition, $options );

        for ( $i = 0; $i <= $limit; $i++ ) {
            if ( !isset( $expressions[$i] ) ) {
                return;
            }

            if ( $i === $limit ) {
                $this->getContinuationManager()->addContinueParam( $this, "from", $expressions[$i]->getID() );

                return;
            }

            $this->getResult()->addValue( "result", null, $expressions[$i]->serialize() );
        }
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "ordering" => [
                ParamValidator::PARAM_TYPE => [
                    Order::Ascending,
                    Order::Descending
                ],
                ParamValidator::PARAM_REQUIRED => false,
                ParamValidator::PARAM_DEFAULT => Order::Ascending
            ],
            "from" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false,
                ParamValidator::PARAM_DEFAULT => 0
            ],
            "limit" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false,
                ParamValidator::PARAM_DEFAULT => 10
            ]
        ];
    }
}