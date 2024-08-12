<?php

namespace Workflows\Api\Delete\Definition;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Delete\ApiDelete;
use Workflows\Api\Delete\ApiDeleteBase;
use Workflows\Api\ModuleRegistry;

final class ApiDeleteDefinition extends ApiDeleteBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiDelete $deleteModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $deleteModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "deletedefinition" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $this->checkUserRightsAny( "workflows-admin-powers" );

        $deletedefinition = $this->getParameter( "deletedefinition" );
        ModuleRegistry::getInstance()->getModule( $this, $deletedefinition )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "deletedefinition" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}