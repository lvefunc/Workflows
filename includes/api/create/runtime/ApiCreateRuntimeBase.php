<?php

namespace Workflows\Api\Create\Runtime;

use ApiBase;
use ApiModuleManager;

abstract class ApiCreateRuntimeBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiCreateRuntime $createRuntimeModule;

    public function __construct( ApiCreateRuntime $createRuntimeModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createRuntimeModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->createRuntimeModule = $createRuntimeModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->createRuntimeModule;
    }
}