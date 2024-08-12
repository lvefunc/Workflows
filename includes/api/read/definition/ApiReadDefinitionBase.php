<?php

namespace Workflows\Api\Read\Definition;

use ApiBase;
use ApiModuleManager;

abstract class ApiReadDefinitionBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiReadDefinition $readDefinitionModule;

    public function __construct( ApiReadDefinition $readDefinitionModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $readDefinitionModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->readDefinitionModule = $readDefinitionModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->readDefinitionModule;
    }
}