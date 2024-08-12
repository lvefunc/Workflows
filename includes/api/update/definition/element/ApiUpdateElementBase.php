<?php

namespace Workflows\Api\Update\Definition\Element;

use ApiBase;
use ApiMain;
use ApiModuleManager;

abstract class ApiUpdateElementBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiUpdateElement $updateElementModule;

    public function __construct( ApiUpdateElement $updateElementModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateElementModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->updateElementModule = $updateElementModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->updateElementModule;
    }
}