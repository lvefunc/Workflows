<?php

namespace Workflows\Api\Update\Value;

use ApiBase;
use ApiModuleManager;

abstract class ApiUpdateValueBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiUpdateValue $updateValueModule;

    public function __construct( ApiUpdateValue $updateValueModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateValueModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->updateValueModule = $updateValueModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->updateValueModule;
    }
}