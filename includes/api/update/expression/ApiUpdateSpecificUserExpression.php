<?php

namespace Workflows\Api\Update\Expression;

use ApiUsageException;
use MediaWiki\MediaWikiServices;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Expression\SpecificUserExpression;

final class ApiUpdateSpecificUserExpression extends ApiUpdateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();
        $mwServices = MediaWikiServices::getInstance();

        $user = $mwServices->getUserFactory()->newFromId( $params["user"] );
        $specificUserExpression = $unitOfWork->findByID( SpecificUserExpression::class, $params["id"] );
        $specificUserExpression->setUser( $user );
        $unitOfWork->commit();

        Utils::setResult( $this, $specificUserExpression );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "user" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}