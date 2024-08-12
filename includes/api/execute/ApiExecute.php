<?php

namespace Workflows\Api\Execute;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\ApiWorkflows;
use Workflows\Api\ApiWorkflowsBase;
use Workflows\Api\ModuleRegistry;

final class ApiExecute extends ApiWorkflowsBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiWorkflows $workflowsModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $workflowsModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "execute" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $execute = $this->getParameter( "execute" );
        ModuleRegistry::getInstance()->getModule( $this, $execute )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "execute" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}