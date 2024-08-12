<?php

namespace Workflows\Api\Update\Expression;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Update\ApiUpdate;
use Workflows\Api\Update\ApiUpdateBase;
use Workflows\Api\ModuleRegistry;

final class ApiUpdateExpression extends ApiUpdateBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiUpdate $updateModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $updateModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "updateexpression" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $this->checkUserRightsAny( "workflows-admin-powers" );

        $updateexpression = $this->getParameter( "updateexpression" );
        ModuleRegistry::getInstance()->getModule( $this, $updateexpression )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "updateexpression" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}