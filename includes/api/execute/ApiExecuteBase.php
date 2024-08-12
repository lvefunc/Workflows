<?php

namespace Workflows\Api\Execute;

use ApiBase;
use ApiModuleManager;

abstract class ApiExecuteBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiExecute $executeModule;

    public function __construct( ApiExecute $executeModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $executeModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->executeModule = $executeModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->executeModule;
    }
}