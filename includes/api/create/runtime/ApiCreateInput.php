<?php

namespace Workflows\Api\Create\Runtime;

use ApiUsageException;
use Exception;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Runtime\Context\Input;
use Workflows\Value\Value;

final class ApiCreateInput extends ApiCreateRuntimeBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     * @throws Exception
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $value = $unitOfWork->findByID( Value::class, $params["value_id"] );
        $input = new Input( $params["name"], $value );

        $unitOfWork->commit();
        $this->getResult()->addValue( null, "result", $input->serialize() );
    }

    public function getAllowedParams() : array {
        return [
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "value_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ]
        ];
    }
}