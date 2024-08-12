<?php

namespace Workflows\Api\Delete;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Expression\Expression;

final class ApiDeleteExpression extends ApiDeleteBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $this->checkUserRightsAny( "workflows-admin-powers" );

        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $expression = $unitOfWork->findByID( Expression::class, $params["id"] );
        $expression->markAsRemoved();
        $unitOfWork->commit();
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}