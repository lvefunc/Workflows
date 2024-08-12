<?php

namespace Workflows\Api\Delete;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\ApiWorkflows;
use Workflows\Api\ApiWorkflowsBase;
use Workflows\Api\ModuleRegistry;

final class ApiDelete extends ApiWorkflowsBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiWorkflows $workflowsModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $workflowsModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "delete" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $delete = $this->getParameter( "delete" );
        ModuleRegistry::getInstance()->getModule( $this, $delete )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "delete" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}