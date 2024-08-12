<?php

namespace Workflows\Api\Update\Value;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Value\Boolean;

final class ApiUpdateBoolean extends ApiUpdateValueBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $boolean = $unitOfWork->findByID( Boolean::class, $params["id"] );
        $boolean->setValue( $params["value"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $boolean );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "value" => [
                ParamValidator::PARAM_TYPE => "boolean",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}