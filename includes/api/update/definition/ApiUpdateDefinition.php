<?php

namespace Workflows\Api\Update\Definition;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Update\ApiUpdate;
use Workflows\Api\Update\ApiUpdateBase;
use Workflows\Api\ModuleRegistry;

final class ApiUpdateDefinition extends ApiUpdateBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiUpdate $updateModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "updatedefinition" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $this->checkUserRightsAny( "workflows-admin-powers" );

        $updatedefinition = $this->getParameter( "updatedefinition" );
        ModuleRegistry::getInstance()->getModule( $this, $updatedefinition )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "updatedefinition" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}