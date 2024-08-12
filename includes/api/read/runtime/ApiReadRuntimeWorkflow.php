<?php

namespace Workflows\Api\Read\Runtime;

use ApiUsageException;
use MiniORM\Expression\Condition;
use MiniORM\Expression\Conjunction;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Enumeration\Order;
use Workflows\Runtime\RtWorkflow;

final class ApiReadRuntimeWorkflow extends ApiReadRuntimeBase {
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
            $workflow = $unitOfWork->findByID( RtWorkflow::class, $params["id"] );
            $this->getResult()->addValue( null, "result", $workflow->serialize() );

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

        if ( isset( $params["prototype_id"] ) ) {
            $conjunction->add( new Condition( "prototype_id", Condition::EqualTo, $params["prototype_id"] ) );
        }

        if ( isset( $params["name"] ) ) {
            $conjunction->add( new Condition( "name", Condition::EqualTo, $params["name"] ) );
        }

        if ( isset( $params["owner"] ) ) {
            $conjunction->add( new Condition( "owner", Condition::EqualTo, $params["owner"] ) );
        }

        $options = [];
        $options["ORDER BY"] = "id " . ( $ordering === Order::Ascending ? "ASC" : "DESC" );
        $options["LIMIT"] = ( $limit + 1 );

        $workflows = $unitOfWork->findMultiple( RtWorkflow::class, $conjunction, $options );

        for ( $i = 0; $i <= $limit; $i++ ) {
            if ( !isset( $workflows[$i] ) ) {
                return;
            }

            if ( $i === $limit ) {
                $this->getContinuationManager()->addContinueParam( $this, "from", $workflows[$i]->getID() );

                return;
            }

            $this->getResult()->addValue( "result", null, $workflows[$i]->serialize() );
        }
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "prototype_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "owner" => [
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