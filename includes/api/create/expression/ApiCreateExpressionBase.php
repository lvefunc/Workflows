<?php

namespace Workflows\Api\Create\Expression;

use ApiBase;
use ApiModuleManager;

abstract class ApiCreateExpressionBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiCreateExpression $createExpressionModule;

    public function __construct( ApiCreateExpression $createExpressionModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createExpressionModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->createExpressionModule = $createExpressionModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->createExpressionModule;
    }
}