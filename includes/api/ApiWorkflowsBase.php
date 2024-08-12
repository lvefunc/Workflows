<?php

namespace Workflows\Api;

use ApiBase;
use ApiModuleManager;

abstract class ApiWorkflowsBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiWorkflows $workflowsModule;

    public function __construct( ApiWorkflows $workflowsModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $workflowsModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->workflowsModule = $workflowsModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->workflowsModule;
    }
}