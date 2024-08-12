<?php

namespace Workflows\Api\Update\Runtime;

use ApiBase;
use ApiModuleManager;

abstract class ApiUpdateRuntimeBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiUpdateRuntime $updateRuntimeModule;

    public function __construct( ApiUpdateRuntime $updateRuntimeModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateRuntimeModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->updateRuntimeModule = $updateRuntimeModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->updateRuntimeModule;
    }
}