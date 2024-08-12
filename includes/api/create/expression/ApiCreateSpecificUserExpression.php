<?php

namespace Workflows\Api\Create\Expression;

use ApiUsageException;
use Exception;
use MediaWiki\MediaWikiServices;
use MiniORM\UnitOfWork;
use User;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Expression\SpecificUserExpression;

final class ApiCreateSpecificUserExpression extends ApiCreateExpressionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();
        $mwServices = MediaWikiServices::getInstance();

        $user = $mwServices->getUserFactory()->newFromId( $params["user"] );
        $specificUserExpression = new SpecificUserExpression( $user );
        $unitOfWork->commit();

        Utils::setResult( $this, $specificUserExpression );
    }

    public function getAllowedParams() : array {
        return [
            "user" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}