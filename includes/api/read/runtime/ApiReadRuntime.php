<?php

namespace Workflows\Api\Read\Runtime;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Read\ApiRead;
use Workflows\Api\Read\ApiReadBase;
use Workflows\Api\ModuleRegistry;

final class ApiReadRuntime extends ApiReadBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiRead $readModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $readModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "readruntime" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $readruntime = $this->getParameter( "readruntime" );
        ModuleRegistry::getInstance()->getModule( $this, $readruntime )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "readruntime" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}