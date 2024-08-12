<?php

namespace Workflows\Api\Delete\Runtime;

use ApiUsageException;
use MiniORM\UnitOfWork;
use MWException;
use ReflectionException;
use RequestContext;
use Wikimedia\ParamValidator\ParamValidator;
use Workflows\Runtime\RtWorkflow;

final class ApiDeleteRuntimeWorkflow extends ApiDeleteRuntimeBase {
    /**
     * @inheritDoc
     * @throws ApiUsageException
     * @throws MWException
     * @throws ReflectionException
     */
    public function execute() {
        $params = $this->extractRequestParams();
        $unitOfWork = UnitOfWork::getInstance();

        $user = RequestContext::getMain()->getUser();
        $rtWorkflow = $unitOfWork->findByID( RtWorkflow::class, $params["id"] );

        if ( !$rtWorkflow->getOwner()->equals( $user ) ) {
            if ( !$rtWorkflow->getOwner()->isAllowed( "workflows-admin-powers" ) ) {
                self::dieWithError(
                    "You are not allowed to delete this runtime workflow since you are not the owner or the admin"
                );
            }
        }

        $rtWorkflow->markAsRemoved();
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