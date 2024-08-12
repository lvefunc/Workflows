<?php

namespace Workflows\Api\Create\Runtime;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Workflow;
use Workflows\Runtime\Context\Input;
use Workflows\Runtime\RtWorkflow;

final class ApiCreateRuntimeWorkflow extends ApiCreateRuntimeBase {
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

        $prototype = $unitOfWork->findByID( Workflow::class, $params["prototype_id"] );
        $inputs = [];

        if ( !is_null( $params["input_ids"] ) ) {
            foreach ( $params["input_ids"] as $id ) {
                $inputs[] = $unitOfWork->findByID( Input::class, $id );
            }
        }

        $rtWorkflow = new RtWorkflow( $prototype, $inputs );
        $unitOfWork->commit();

        Utils::setResult( $this, $rtWorkflow );
    }

    public function getAllowedParams() : array {
        return [
            "prototype_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "input_ids" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false,
                ParamValidator::PARAM_ISMULTI => true
            ]
        ];
    }
}