<?php

namespace Workflows\Api\Read;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\ApiWorkflows;
use Workflows\Api\ApiWorkflowsBase;
use Workflows\Api\ModuleRegistry;

final class ApiRead extends ApiWorkflowsBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiWorkflows $workflowsModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $workflowsModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "read" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $read = $this->getParameter( "read" );
        ModuleRegistry::getInstance()->getModule( $this, $read )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "read" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}