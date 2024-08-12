<?php

namespace Workflows\Api\Delete;

use ApiBase;
use ApiModuleManager;

abstract class ApiDeleteBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiDelete $deleteModule;

    public function __construct( ApiDelete $deleteModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $deleteModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->deleteModule = $deleteModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->deleteModule;
    }
}