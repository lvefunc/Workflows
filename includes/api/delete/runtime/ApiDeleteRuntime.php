<?php

namespace Workflows\Api\Delete\Runtime;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Delete\ApiDelete;
use Workflows\Api\Delete\ApiDeleteBase;
use Workflows\Api\ModuleRegistry;

final class ApiDeleteRuntime extends ApiDeleteBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiDelete $deleteModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $deleteModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "deleteruntime" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $deleteruntime = $this->getParameter( "deleteruntime" );
        ModuleRegistry::getInstance()->getModule( $this, $deleteruntime )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "deleteruntime" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}