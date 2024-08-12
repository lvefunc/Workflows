<?php

namespace Workflows\Api\Create;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\ApiWorkflows;
use Workflows\Api\ApiWorkflowsBase;
use Workflows\Api\ModuleRegistry;

final class ApiCreate extends ApiWorkflowsBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiWorkflows $workflowsModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $workflowsModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "create" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $create = $this->getParameter( "create" );
        ModuleRegistry::getInstance()->getModule( $this, $create )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "create" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}