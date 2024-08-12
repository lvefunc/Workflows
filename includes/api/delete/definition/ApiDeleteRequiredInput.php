<?php

namespace Workflows\Api\Delete\Definition;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Definition\RequiredInput;

final class ApiDeleteRequiredInput extends ApiDeleteDefinitionBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $requiredInput = $unitOfWork->findByID( RequiredInput::class, $params["id"] );
        $requiredInput->markAsRemoved();

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