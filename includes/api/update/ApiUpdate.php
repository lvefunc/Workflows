<?php

namespace Workflows\Api\Update;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\ApiWorkflows;
use Workflows\Api\ApiWorkflowsBase;
use Workflows\Api\ModuleRegistry;

final class ApiUpdate extends ApiWorkflowsBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiWorkflows $workflowsModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $workflowsModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "update" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $update = $this->getParameter( "update" );
        ModuleRegistry::getInstance()->getModule( $this, $update )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "update" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}