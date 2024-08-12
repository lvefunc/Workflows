<?php

namespace Workflows\Api\Update\Expression;

use ApiBase;
use ApiModuleManager;

abstract class ApiUpdateExpressionBase extends ApiBase {
    protected ApiModuleManager $moduleManager;
    protected ApiUpdateExpression $updateExpressionModule;

    public function __construct( ApiUpdateExpression $updateExpressionModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateExpressionModule->getMain(), $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        $this->updateExpressionModule = $updateExpressionModule;
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getParent() {
        return $this->updateExpressionModule;
    }
}