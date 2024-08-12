<?php

namespace Workflows\Api\Update\Value;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Update\ApiUpdate;
use Workflows\Api\Update\ApiUpdateBase;
use Workflows\Api\ModuleRegistry;

final class ApiUpdateValue extends ApiUpdateBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiUpdate $updateModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "updatevalue" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $updatevalue = $this->getParameter( "updatevalue" );
        ModuleRegistry::getInstance()->getModule( $this, $updatevalue )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "updatevalue" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}