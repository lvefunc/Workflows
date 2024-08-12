<?php

namespace Workflows\Api\Create;

use ApiBase;
use ApiModuleManager;

abstract class ApiCreateBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiCreate $createModule;

    public function __construct( ApiCreate $createModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->createModule = $createModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->createModule;
    }
}