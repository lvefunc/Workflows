<?php

namespace Workflows\Api\Delete\Definition;

use ApiBase;
use ApiModuleManager;

abstract class ApiDeleteDefinitionBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiDeleteDefinition $deleteDefinitionModule;

    public function __construct( ApiDeleteDefinition $deleteDefinitionModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $deleteDefinitionModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->deleteDefinitionModule = $deleteDefinitionModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->deleteDefinitionModule;
    }
}