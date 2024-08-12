<?php

namespace Workflows\Api\Create\Value;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Value\Boolean;

final class ApiCreateBoolean extends ApiCreateValueBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $boolean = new Boolean( $params["value"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $boolean );
    }

    public function getAllowedParams() : array {
        return [
            "value" => [
                ParamValidator::PARAM_TYPE => "boolean",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}