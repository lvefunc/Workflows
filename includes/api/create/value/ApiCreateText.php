<?php

namespace Workflows\Api\Create\Value;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Value\Text;

final class ApiCreateText extends ApiCreateValueBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $text = new Text( $params["value"] );
        $unitOfWork->commit();

        Utils::setResult( $this, $text );
    }

    public function getAllowedParams() : array {
        return [
            "value" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}