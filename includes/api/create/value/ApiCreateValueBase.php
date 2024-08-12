<?php

namespace Workflows\Api\Create\Value;

use ApiBase;
use ApiModuleManager;

abstract class ApiCreateValueBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiCreateValue $createValueModule;

    public function __construct( ApiCreateValue $createValueModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createValueModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->createValueModule = $createValueModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->createValueModule;
    }
}