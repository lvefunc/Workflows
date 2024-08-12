<?php

namespace Workflows\Api\Create\Definition\Element;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Create\Definition\ApiCreateDefinition;
use Workflows\Api\Create\Definition\ApiCreateDefinitionBase;
use Workflows\Api\ModuleRegistry;

final class ApiCreateElement extends ApiCreateDefinitionBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiCreateDefinition $createDefinitionModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createDefinitionModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "createelement" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $createelement = $this->getParameter( "createelement" );
        ModuleRegistry::getInstance()->getModule( $this, $createelement )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "createelement" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}