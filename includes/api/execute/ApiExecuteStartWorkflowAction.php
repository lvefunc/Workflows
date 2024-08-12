<?php

namespace Workflows\Api\Execute;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Runtime\Action\StartWorkflowAction;
use Workflows\Runtime\RtWorkflow;

final class ApiExecuteStartWorkflowAction extends ApiExecuteBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $rtWorkflow = $unitOfWork->findByID( RtWorkflow::class, $params["id"] );
        $startWorkflowAction = new StartWorkflowAction( $rtWorkflow );
        $startWorkflowAction->execute();
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}