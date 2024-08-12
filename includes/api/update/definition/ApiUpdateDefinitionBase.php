<?php

namespace Workflows\Api\Update\Definition;

use ApiBase;
use ApiModuleManager;

abstract class ApiUpdateDefinitionBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiUpdateDefinition $updateDefinitionModule;

    public function __construct( ApiUpdateDefinition $updateDefinitionModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateDefinitionModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->updateDefinitionModule = $updateDefinitionModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->updateDefinitionModule;
    }
}