<?php

namespace Workflows\Api\Create\Definition;

use ApiBase;
use ApiModuleManager;

abstract class ApiCreateDefinitionBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiCreateDefinition $createDefinitionModule;

    public function __construct( ApiCreateDefinition $createDefinitionModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createDefinitionModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->createDefinitionModule = $createDefinitionModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->createDefinitionModule;
    }
}