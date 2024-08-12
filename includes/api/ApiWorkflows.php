<?php

namespace Workflows\Api;

use ApiBase;
use ApiContinuationManager;
use ApiMain;
use ApiModuleManager;
use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;

final class ApiWorkflows extends ApiBase {
    private ApiModuleManager $moduleManager;

    /**
     * @throws MWException
     */
    public function __construct( ApiMain $mainModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $mainModule, $moduleName, $modulePrefix );
        $this->moduleManager = new ApiModuleManager( $this );
        ModuleRegistry::getInstance()->instantiateModules( $this, "operation" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $this->checkUserRightsAny( "workflows-user-powers" );

        $allModules = ModuleRegistry::getInstance()->getAllModules();
        $continuationManager = new ApiContinuationManager( $this, $allModules );
        $this->setContinuationManager( $continuationManager );

        $operation = $this->getParameter( "operation" );
        ModuleRegistry::getInstance()->getModule( $this, $operation )->execute();

        $this->getContinuationManager()->setContinuationIntoResult( $this->getResult() );
    }

    public function getModuleManager() : ?ApiModuleManager {
        return $this->moduleManager;
    }

    public function getAllowedParams() : array {
        return [
            "operation" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}