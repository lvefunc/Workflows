<?php

namespace Workflows\Api\Update\Definition\Element;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Update\Definition\ApiUpdateDefinition;
use Workflows\Api\Update\Definition\ApiUpdateDefinitionBase;
use Workflows\Api\ModuleRegistry;

final class ApiUpdateElement extends ApiUpdateDefinitionBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiUpdateDefinition $updateDefinitionModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateDefinitionModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "updateelement" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $updateelement = $this->getParameter( "updateelement" );
        ModuleRegistry::getInstance()->getModule( $this, $updateelement )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "updateelement" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}