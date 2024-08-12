<?php

namespace Workflows\Api\Create\Definition;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Element\Element;
use Workflows\Definition\Transition\Transition;
use Workflows\Definition\Workflow;
use Workflows\Expression\Expression;

final class ApiCreateTransition extends ApiCreateDefinitionBase {
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

        $source = $unitOfWork->findByID( Element::class, $params["source_id"] );
        $target = $unitOfWork->findByID( Element::class, $params["target_id"] );
        $transition = new Transition( $source, $target );

        if ( isset( $params["logical_expr_id"] ) ) {
            $logicalExpression = $unitOfWork->findByID( Expression::class, $params["logical_expr_id"] );
            $transition->setLogicalExpression( $logicalExpression );
        }

        $workflow = $unitOfWork->findByID( Workflow::class, $params["workflow_id"] );
        $workflow->addTransition( $transition );
        $unitOfWork->commit();

        Utils::setResult( $this, $transition );
    }

    public function getAllowedParams() : array {
        return [
            "workflow_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "source_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "target_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "logical_expr_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ]
        ];
    }
}