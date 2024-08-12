<?php

namespace Workflows\Api\Create\Runtime;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Create\ApiCreate;
use Workflows\Api\Create\ApiCreateBase;
use Workflows\Api\ModuleRegistry;

final class ApiCreateRuntime extends ApiCreateBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiCreate $createModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "createruntime" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $createruntime = $this->getParameter( "createruntime" );
        ModuleRegistry::getInstance()->getModule( $this, $createruntime )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "createruntime" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}