<?php

namespace Workflows\Api\Read\Definition;

use ApiUsageException;
use MiniORM\Expression\Condition;
use MiniORM\Expression\Conjunction;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Definition\Element\Element;
use Workflows\Enumeration\Order;

final class ApiReadElement extends ApiReadDefinitionBase {
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
            $element = $unitOfWork->findByID( Element::class, $params["id"] );
            $this->getResult()->addValue( null, "result", $element->serialize() );
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

        if ( isset( $params["name"] ) ) {
            $conjunction->add( new Condition( "name", Condition::EqualTo, $params["name"] ) );
        }

        if ( isset( $params["workflow_id"] ) ) {
            $conjunction->add( new Condition( "workflow_id", Condition::EqualTo, $params["workflow_id"] ) );
        }

        $options = [];
        $options["ORDER BY"] = "id " . ( $ordering === Order::Ascending ? "ASC" : "DESC" );
        $options["LIMIT"] = ( $limit + 1 );

        $elements = $unitOfWork->findMultiple( Element::class, $conjunction, $options );

        for ( $i = 0; $i <= $limit; $i++ ) {
            if ( !isset( $elements[$i] ) ) {
                return;
            }

            if ( $i === $limit ) {
                $this->getContinuationManager()->addContinueParam( $this, "from", $elements[$i]->getID() );

                return;
            }

            $this->getResult()->addValue( "result", null, $elements[$i]->serialize() );
        }

    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
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