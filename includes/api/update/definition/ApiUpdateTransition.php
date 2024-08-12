<?php

namespace Workflows\Api\Update\Definition;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Element\Element;
use Workflows\Definition\Transition\Transition;
use Workflows\Definition\Workflow;
use Workflows\Expression\Expression;

final class ApiUpdateTransition extends ApiUpdateDefinitionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $transition = $unitOfWork->findByID( Transition::class, $params["id"] );

        if ( isset( $params["workflow_id"] ) ) {
            $workflow = $unitOfWork->findByID( Workflow::class, $params["workflow_id"] );
            $workflow->addTransition( $transition );
        }

        if ( isset( $params["source_id"] ) ) {
            $source = $unitOfWork->findByID( Element::class, $params["source_id"] );
            $transition->setSource( $source );
        }

        if ( isset( $params["target_id"] ) ) {
            $target = $unitOfWork->findByID( Element::class, $params["target_id"] );
            $transition->setTarget( $target );
        }

        if ( isset( $params["logical_expr_id"] ) ) {
            $logicalExpression = $unitOfWork->findByID( Expression::class, $params["logical_expr_id"] );
            $transition->setLogicalExpression( $logicalExpression );
        }

        $unitOfWork->commit();

        Utils::setResult( $this, $transition );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "workflow_id" => [
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
            "logical_expr_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ]
        ];
    }
}