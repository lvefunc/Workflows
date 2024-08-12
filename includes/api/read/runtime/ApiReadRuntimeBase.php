<?php

namespace Workflows\Api\Read\Runtime;

use ApiBase;
use ApiModuleManager;

abstract class ApiReadRuntimeBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiReadRuntime $readRuntimeModule;

    public function __construct( ApiReadRuntime $readRuntimeModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $readRuntimeModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->readRuntimeModule = $readRuntimeModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->readRuntimeModule;
    }
}