<?php

namespace Workflows\Api\Create\Definition;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Definition\Workflow;

final class ApiCreateWorkflow extends ApiCreateDefinitionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $workflow = new Workflow( $params["name"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $workflow );
    }

    public function getAllowedParams() : array {
        return [
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}