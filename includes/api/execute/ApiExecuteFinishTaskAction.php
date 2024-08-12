<?php

namespace Workflows\Api\Execute;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Runtime\Action\FinishTaskAction;
use Workflows\Runtime\Task\RtTask;

final class ApiExecuteFinishTaskAction extends ApiExecuteBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $task = $unitOfWork->findByID( RtTask::class, $params["id"] );

        $finishTaskAction = new FinishTaskAction( $task );
        $finishTaskAction->execute();
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