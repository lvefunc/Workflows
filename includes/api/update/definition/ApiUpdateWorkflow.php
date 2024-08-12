<?php

namespace Workflows\Api\Update\Definition;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Workflow;

final class ApiUpdateWorkflow extends ApiUpdateDefinitionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $workflow = $unitOfWork->findByID( Workflow::class, $params["id"] );
        $workflow->setWord( $params["name"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $workflow );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}