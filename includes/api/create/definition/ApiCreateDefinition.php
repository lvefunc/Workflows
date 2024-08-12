<?php

namespace Workflows\Api\Create\Definition;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Create\ApiCreate;
use Workflows\Api\Create\ApiCreateBase;
use Workflows\Api\ModuleRegistry;

final class ApiCreateDefinition extends ApiCreateBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiCreate $createModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "createdefinition" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $this->checkUserRightsAny( "workflows-admin-powers" );

        $createdefinition = $this->getParameter( "createdefinition" );
        ModuleRegistry::getInstance()->getModule( $this, $createdefinition )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "createdefinition" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}