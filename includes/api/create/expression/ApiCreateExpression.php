<?php

namespace Workflows\Api\Create\Expression;

use ApiUsageException;
use MWException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Create\ApiCreate;
use Workflows\Api\Create\ApiCreateBase;
use Workflows\Api\ModuleRegistry;

final class ApiCreateExpression extends ApiCreateBase {
    /**
     * @throws MWException
     */
    public function __construct( ApiCreate $createModule, $moduleName, $modulePrefix = '' ) {
        parent::__construct( $createModule, $moduleName, $modulePrefix );
        ModuleRegistry::getInstance()->instantiateModules( $this, "createexpression" );
    }

    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     */
    public function execute() {
        $this->checkUserRightsAny( "workflows-admin-powers" );

        $createexpression = $this->getParameter( "createexpression" );
        ModuleRegistry::getInstance()->getModule( $this, $createexpression )->execute();
    }

    public function getAllowedParams() : array {
        return [
            "createexpression" => [
                ParamValidator::PARAM_TYPE => "submodule",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}