<?php

namespace Workflows\Api\Update\Runtime;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Update\ApiUpdate;
use Workflows\Api\Update\ApiUpdateBase;
use Workflows\Api\ModuleRegistry;

final class ApiUpdateRuntime extends ApiUpdateBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiUpdate $updateModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "updateruntime" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $updateruntime = $this->getParameter( "updateruntime" );
        ModuleRegistry::getInstance()->getModule( $this, $updateruntime )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "updateruntime" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}