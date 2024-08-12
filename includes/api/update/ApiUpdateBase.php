<?php

namespace Workflows\Api\Update;

use ApiBase;
use ApiModuleManager;

abstract class ApiUpdateBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiUpdate $updateModule;

    public function __construct( ApiUpdate $updateModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->updateModule = $updateModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->updateModule;
    }
}