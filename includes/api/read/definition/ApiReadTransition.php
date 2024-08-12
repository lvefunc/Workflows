<?php

namespace Workflows\Api\Read\Definition;

use ApiUsageException;
use MiniORM\Expression\Condition;
use MiniORM\Expression\Conjunction;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Definition\Transition\Transition;
use Workflows\Enumeration\Order;

final class ApiReadTransition extends ApiReadDefinitionBase {
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
            $transition = $unitOfWork->findByID( Transition::class, $params["id"] );
            $this->getResult()->addValue( null, "result", $transition->serialize() );

            return;
        }

        $ordering = $params["ordering"] ?? Order::Ascending;
        $from = $params["from"] ?? 0;
        $limit = $params["limit"] ?? 10;

        $conjunction = new Conjunction();

        switch ( $ordering ) {
            case Order::Ascending:
                $conjunction->add( new Condition( "id", Condition::MoreThanOrEqualTo, $from ) );
                break;
            case Order::Descending:
                $operand = $from === 0 ? Condition::MoreThanOrEqualTo : Condition::LessThanOrEqualTo;
                $conjunction->add( new Condition( "id", $operand, $from ) );
                break;
        }

        if ( isset( $params["source_id"] ) ) {
            $conjunction->add( new Condition( "source_id", Condition::EqualTo, $params["source_id"] ) );
        }

        if ( isset( $params["target_id"] ) ) {
            $conjunction->add( new Condition( "target_id", Condition::EqualTo, $params["target_id"] ) );
        }

        if ( isset( $params["workflow_id"] ) ) {
            $conjunction->add( new Condition( "workflow_id", Condition::EqualTo, $params["workflow_id"] ) );
        }

        $options = [];
        $options["ORDER BY"] = "id " . ( $ordering === Order::Ascending ? "ASC" : "DESC" );
        $options["LIMIT"] = ( $limit + 1 );

        $transitions = $unitOfWork->findMultiple( Transition::class, $conjunction, $options );

        for ( $i = 0; $i <= $limit; $i++ ) {
            if ( !isset( $transitions[$i] ) ) {
                return;
            }

            if ( $i === $limit ) {
                $this->getContinuationManager()->addContinueParam( $this, "from", $transitions[$i]->getID() );

                return;
            }

            $this->getResult()->addValue( "result", null, $transitions[$i]->serialize() );
        }
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "source_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "target_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "workflow_id" => [
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