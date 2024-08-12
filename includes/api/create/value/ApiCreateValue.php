<?php

namespace Workflows\Api\Create\Value;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Create\ApiCreate;
use Workflows\Api\Create\ApiCreateBase;
use Workflows\Api\ModuleRegistry;

final class ApiCreateValue extends ApiCreateBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiCreate $createModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "createvalue" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $createvalue = $this->getParameter( "createvalue" );
        ModuleRegistry::getInstance()->getModule( $this, $createvalue )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "createvalue" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}