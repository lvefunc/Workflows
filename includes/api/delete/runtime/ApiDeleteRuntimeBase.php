<?php

namespace Workflows\Api\Delete\Runtime;

use ApiBase;
use ApiModuleManager;

abstract class ApiDeleteRuntimeBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiDeleteRuntime $deleteRuntimeModule;

    public function __construct( ApiDeleteRuntime $deleteRuntimeModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $deleteRuntimeModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->deleteRuntimeModule = $deleteRuntimeModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->deleteRuntimeModule;
    }
}