<?php

namespace Workflows\Api\Read;

use ApiBase;
use ApiModuleManager;

abstract class ApiReadBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiRead $readModule;

    public function __construct( ApiRead $readModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $readModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->readModule = $readModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->readModule;
    }
}