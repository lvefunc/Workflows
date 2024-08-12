<?php

namespace Workflows\Api\Read\Definition;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Read\ApiRead;
use Workflows\Api\Read\ApiReadBase;
use Workflows\Api\ModuleRegistry;

final class ApiReadDefinition extends ApiReadBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiRead $readModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $readModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "readdefinition" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $readdefinition = $this->getParameter( "readdefinition" );
        ModuleRegistry::getInstance()->getModule( $this, $readdefinition )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "readdefinition" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}