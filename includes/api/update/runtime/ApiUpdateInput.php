<?php

namespace Workflows\Api\Update\Runtime;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use RequestContext;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Api\Utils;
use Workflows\Runtime\Context\Input;
use Workflows\Value\Value;

final class ApiUpdateInput extends ApiUpdateRuntimeBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $input = $unitOfWork->findByID( Input::class, $params["id"] );

        if ( isset( $params["name"] ) ) {
            $input->setWord( $params["name"] );
        }

        if ( isset( $params["value_id"] ) ) {
            $value = $unitOfWork->findByID( Value::class, $params["value_id"] );
            $input->setValue( $value );
        }

        $unitOfWork->commit();
        $this->getResult()->addValue( null, "result", $input->serialize() );
    }

    public function getAllowedParams() : array {
        return [
            "id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => true
            ],
            "name" => [
                ParamValidator::PARAM_TYPE => "string",
                ParamValidator::PARAM_REQUIRED => false
            ],
            "value_id" => [
                ParamValidator::PARAM_TYPE => "integer",
                ParamValidator::PARAM_REQUIRED => false
            ]
        ];
    }
}