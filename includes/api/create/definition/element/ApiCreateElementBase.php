<?php

namespace Workflows\Api\Create\Definition\Element;

use ApiBase;
use ApiModuleManager;

abstract class ApiCreateElementBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiCreateElement $createElementModule;

    public function __construct( ApiCreateElement $createElementModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createElementModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->createElementModule = $createElementModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->createElementModule;
    }
}