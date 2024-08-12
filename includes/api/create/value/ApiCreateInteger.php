<?php

namespace Workflows\Api\Create\Value;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Value\Integer;

final class ApiCreateInteger extends ApiCreateValueBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $integer = new Integer( $params["value"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $integer );
    }

    public function getAllowedParams() : array {
        return [
            "value" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}