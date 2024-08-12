<?php

namespace Workflows;

use MiniORM\Schema\SchemaUpdater;
use MWException;
use ReflectionException;
use Workflows\Api\ApiWorkflows;
use Workflows\Api\Create\ApiCreate;
use Workflows\Api\Create\Definition\ApiCreateDefinition;
use Workflows\Api\Create\Definition\ApiCreateRequiredInput;
use Workflows\Api\Create\Definition\ApiCreateTransition;
use Workflows\Api\Create\Definition\ApiCreateWorkflow;
use Workflows\Api\Create\Definition\Element\ApiCreateElement;
use Workflows\Api\Create\Definition\Element\ApiCreateEvent;
use Workflows\Api\Create\Definition\Element\ApiCreateExclusiveGateway;
use Workflows\Api\Create\Definition\Element\ApiCreateInclusiveGateway;
use Workflows\Api\Create\Definition\Element\ApiCreateParallelGateway;
use Workflows\Api\Create\Expression\ApiCreateComparison;
use Workflows\Api\Create\Expression\ApiCreateConjunction;
use Workflows\Api\Create\Expression\ApiCreateDisjunction;
use Workflows\Api\Create\Expression\ApiCreateExpression;
use Workflows\Api\Create\Expression\ApiCreateRuntimeUserExpression;
use Workflows\Api\Create\Expression\ApiCreateSpecificUserExpression;
use Workflows\Api\Create\Expression\ApiCreateValueExpression;
use Workflows\Api\Create\Expression\ApiCreateVariableExpression;
use Workflows\Api\Create\Runtime\ApiCreateInput;
use Workflows\Api\Create\Runtime\ApiCreateRuntime;
use Workflows\Api\Create\Runtime\ApiCreateRuntimeWorkflow;
use Workflows\Api\Create\Value\ApiCreateBoolean;
use Workflows\Api\Create\Value\ApiCreateInteger;
use Workflows\Api\Create\Value\ApiCreateText;
use Workflows\Api\Create\Value\ApiCreateValue;
use Workflows\Api\Delete\ApiDelete;
use Workflows\Api\Delete\ApiDeleteExpression;
use Workflows\Api\Delete\ApiDeleteValue;
use Workflows\Api\Delete\Definition\ApiDeleteDefinition;
use Workflows\Api\Delete\Definition\ApiDeleteElement;
use Workflows\Api\Delete\Definition\ApiDeleteRequiredInput;
use Workflows\Api\Delete\Definition\ApiDeleteTransition;
use Workflows\Api\Delete\Definition\ApiDeleteWorkflow;
use Workflows\Api\Delete\Runtime\ApiDeleteInput;
use Workflows\Api\Delete\Runtime\ApiDeleteRuntime;
use Workflows\Api\Delete\Runtime\ApiDeleteRuntimeWorkflow;
use Workflows\Api\Read\ApiRead;
use Workflows\Api\Read\ApiReadExpression;
use Workflows\Api\Read\ApiReadValue;
use Workflows\Api\Read\Definition\ApiReadDefinition;
use Workflows\Api\Read\Definition\ApiReadElement;
use Workflows\Api\Read\Definition\ApiReadRequiredInput;
use Workflows\Api\Read\Definition\ApiReadTransition;
use Workflows\Api\Read\Definition\ApiReadWorkflow;
use Workflows\Api\Read\Runtime\ApiReadInput;
use Workflows\Api\Read\Runtime\ApiReadRuntime;
use Workflows\Api\Read\Runtime\ApiReadRuntimeWorkflow;
use Workflows\Api\Read\Runtime\ApiReadTask;
use Workflows\Api\Update\ApiUpdate;
use Workflows\Api\Update\Definition\ApiUpdateDefinition;
use Workflows\Api\Update\Definition\ApiUpdateRequiredInput;
use Workflows\Api\Update\Definition\ApiUpdateTransition;
use Workflows\Api\Update\Definition\ApiUpdateWorkflow;
use Workflows\Api\Update\Definition\Element\ApiUpdateElement;
use Workflows\Api\Update\Definition\Element\ApiUpdateEvent;
use Workflows\Api\Update\Definition\Element\ApiUpdateExclusiveGateway;
use Workflows\Api\Update\Definition\Element\ApiUpdateInclusiveGateway;
use Workflows\Api\Update\Definition\Element\ApiUpdateParallelGateway;
use Workflows\Api\Update\Expression\ApiUpdateComparison;
use Workflows\Api\Update\Expression\ApiUpdateConjunction;
use Workflows\Api\Update\Expression\ApiUpdateDisjunction;
use Workflows\Api\Update\Expression\ApiUpdateExpression;
use Workflows\Api\Update\Expression\ApiUpdateRuntimeUserExpression;
use Workflows\Api\Update\Expression\ApiUpdateSpecificUserExpression;
use Workflows\Api\Update\Expression\ApiUpdateValueExpression;
use Workflows\Api\Update\Expression\ApiUpdateVariableExpression;
use Workflows\Api\Update\Runtime\ApiUpdateInput;
use Workflows\Api\Update\Runtime\ApiUpdateRuntime;
use Workflows\Api\Update\Value\ApiUpdateBoolean;
use Workflows\Api\Update\Value\ApiUpdateInteger;
use Workflows\Api\Update\Value\ApiUpdateText;
use Workflows\Api\Update\Value\ApiUpdateValue;
use Workflows\Api\Execute\ApiExecute;
use Workflows\Api\Execute\ApiExecuteFinishTaskAction;
use Workflows\Api\Execute\ApiExecuteStartWorkflowAction;
use Workflows\Api\ModuleRegistry;
use Workflows\Definition\Element\Activity\UserActivity;
use Workflows\Definition\Element\Element;
use Workflows\Definition\Element\Event;
use Workflows\Definition\Element\Gateway\ExclusiveGateway;
use Workflows\Definition\Element\Gateway\Gateway;
use Workflows\Definition\Element\Gateway\InclusiveGateway;
use Workflows\Definition\Element\Gateway\ParallelGateway;
use Workflows\Definition\RequiredInput;
use Workflows\Definition\Transition\Transition;
use Workflows\Definition\Workflow;
use Workflows\Expression\Comparison;
use Workflows\Expression\Conjunction;
use Workflows\Expression\Disjunction;
use Workflows\Expression\Expression;
use Workflows\Expression\Operation;
use Workflows\Expression\RuntimeUserExpression;
use Workflows\Expression\SpecificUserExpression;
use Workflows\Expression\ValueExpression;
use Workflows\Expression\VariableExpression;
use Workflows\Notification\NewTaskPresentationModel;
use Workflows\Notification\StateChangedPresentationModel;
use Workflows\Runtime\Context\Context;
use Workflows\Runtime\Context\Input;
use Workflows\Runtime\Context\Token;
use Workflows\Runtime\Context\Variable;
use Workflows\Runtime\Element\Activity\RtUserActivity;
use Workflows\Runtime\Element\Gateway\RtExclusiveGateway;
use Workflows\Runtime\Element\Gateway\RtGateway;
use Workflows\Runtime\Element\Gateway\RtInclusiveGateway;
use Workflows\Runtime\Element\Gateway\RtParallelGateway;
use Workflows\Runtime\Element\RtElement;
use Workflows\Runtime\Element\RtEvent;
use Workflows\Runtime\RtState;
use Workflows\Runtime\RtWorkflow;
use Workflows\Runtime\Task\RtTask;
use Workflows\Runtime\Transition\RtTransition;
use Workflows\Value\Boolean;
use Workflows\Value\Integer;
use Workflows\Value\Text;
use Workflows\Value\Value;

class Hooks {
    /**
     * @throws ReflectionException
     * @throws MWException
     */
    public static function registerSchemaUpdates( SchemaUpdater $schemaUpdater ) {
        $schemaUpdater->register( Value::class );
        $schemaUpdater->register( Boolean::class );
        $schemaUpdater->register( Integer::class );
        $schemaUpdater->register( Text::class );

        $schemaUpdater->register( Expression::class );
        $schemaUpdater->register( VariableExpression::class );
        $schemaUpdater->register( ValueExpression::class );
        $schemaUpdater->register( SpecificUserExpression::class );
        $schemaUpdater->register( RuntimeUserExpression::class );
        $schemaUpdater->register( Operation::class );
        $schemaUpdater->register( Comparison::class );
        $schemaUpdater->register( Conjunction::class );
        $schemaUpdater->register( Disjunction::class );

        $schemaUpdater->register( Workflow::class );
        $schemaUpdater->register( Element::class );
        $schemaUpdater->register( Event::class );
        $schemaUpdater->register( Gateway::class );
        $schemaUpdater->register( ParallelGateway::class );
        $schemaUpdater->register( ExclusiveGateway::class );
        $schemaUpdater->register( InclusiveGateway::class );
        $schemaUpdater->register( UserActivity::class );
        $schemaUpdater->register( Transition::class );
        $schemaUpdater->register( RequiredInput::class );

        $schemaUpdater->register( Context::class );
        $schemaUpdater->register( Input::class );
        $schemaUpdater->register( Variable::class );
        $schemaUpdater->register( Token::class );

        $schemaUpdater->register( RtState::class );
        $schemaUpdater->register( RtWorkflow::class );
        $schemaUpdater->register( RtElement::class );
        $schemaUpdater->register( RtEvent::class );
        $schemaUpdater->register( RtGateway::class );
        $schemaUpdater->register( RtParallelGateway::class );
        $schemaUpdater->register( RtExclusiveGateway::class );
        $schemaUpdater->register( RtInclusiveGateway::class );
        $schemaUpdater->register( RtUserActivity::class );
        $schemaUpdater->register( RtTask::class );
        $schemaUpdater->register( RtTransition::class );
    }

    public static function registerWorkflowsModules( ModuleRegistry $moduleRegistry ) {
        $moduleRegistry->register( ApiWorkflows::class, "read", ApiRead::class );
        $moduleRegistry->register( ApiWorkflows::class, "create", ApiCreate::class );
        $moduleRegistry->register( ApiWorkflows::class, "update", ApiUpdate::class );
        $moduleRegistry->register( ApiWorkflows::class, "delete", ApiDelete::class );
        $moduleRegistry->register( ApiWorkflows::class, "execute", ApiExecute::class );

        $moduleRegistry->register( ApiRead::class, "value", ApiReadValue::class );
        $moduleRegistry->register( ApiRead::class, "expression", ApiReadExpression::class );
        $moduleRegistry->register( ApiRead::class, "definition", ApiReadDefinition::class );
        $moduleRegistry->register( ApiRead::class, "runtime", ApiReadRuntime::class );
        $moduleRegistry->register( ApiReadDefinition::class, "workflow", ApiReadWorkflow::class );
        $moduleRegistry->register( ApiReadDefinition::class, "element", ApiReadElement::class );
        $moduleRegistry->register( ApiReadDefinition::class, "transition", ApiReadTransition::class );
        $moduleRegistry->register( ApiReadDefinition::class, "requiredinput", ApiReadRequiredInput::class );
        $moduleRegistry->register( ApiReadRuntime::class, "task", ApiReadTask::class );
        $moduleRegistry->register( ApiReadRuntime::class, "input", ApiReadInput::class );
        $moduleRegistry->register( ApiReadRuntime::class, "runtimeworkflow", ApiReadRuntimeWorkflow::class );

        $moduleRegistry->register( ApiCreate::class, "value", ApiCreateValue::class );
        $moduleRegistry->register( ApiCreate::class, "expression", ApiCreateExpression::class );
        $moduleRegistry->register( ApiCreate::class, "definition", ApiCreateDefinition::class );
        $moduleRegistry->register( ApiCreate::class, "runtime", ApiCreateRuntime::class );
        $moduleRegistry->register( ApiCreateValue::class, "boolean", ApiCreateBoolean::class );
        $moduleRegistry->register( ApiCreateValue::class, "integer", ApiCreateInteger::class );
        $moduleRegistry->register( ApiCreateValue::class, "text", ApiCreateText::class );
        $moduleRegistry->register( ApiCreateExpression::class, "variable", ApiCreateVariableExpression::class );
        $moduleRegistry->register( ApiCreateExpression::class, "value", ApiCreateValueExpression::class );
        $moduleRegistry->register( ApiCreateExpression::class, "specificuser", ApiCreateSpecificUserExpression::class );
        $moduleRegistry->register( ApiCreateExpression::class, "runtimeuser", ApiCreateRuntimeUserExpression::class );
        $moduleRegistry->register( ApiCreateExpression::class, "comparison", ApiCreateComparison::class );
        $moduleRegistry->register( ApiCreateExpression::class, "conjunction", ApiCreateConjunction::class );
        $moduleRegistry->register( ApiCreateExpression::class, "disjunction", ApiCreateDisjunction::class );
        $moduleRegistry->register( ApiCreateDefinition::class, "workflow", ApiCreateWorkflow::class );
        $moduleRegistry->register( ApiCreateDefinition::class, "element", ApiCreateElement::class );
        $moduleRegistry->register( ApiCreateDefinition::class, "transition", ApiCreateTransition::class );
        $moduleRegistry->register( ApiCreateDefinition::class, "requiredinput", ApiCreateRequiredInput::class );
        $moduleRegistry->register( ApiCreateElement::class, "event", ApiCreateEvent::class );
        $moduleRegistry->register( ApiCreateElement::class, "parallelgateway", ApiCreateParallelGateway::class );
        $moduleRegistry->register( ApiCreateElement::class, "exclusivegateway", ApiCreateExclusiveGateway::class );
        $moduleRegistry->register( ApiCreateElement::class, "inclusivegateway", ApiCreateInclusiveGateway::class );
        $moduleRegistry->register( ApiCreateRuntime::class, "input", ApiCreateInput::class );
        $moduleRegistry->register( ApiCreateRuntime::class, "runtimeworkflow", ApiCreateRuntimeWorkflow::class );

        $moduleRegistry->register( ApiUpdate::class, "value", ApiUpdateValue::class );
        $moduleRegistry->register( ApiUpdate::class, "expression", ApiUpdateExpression::class );
        $moduleRegistry->register( ApiUpdate::class, "definition", ApiUpdateDefinition::class );
        $moduleRegistry->register( ApiUpdate::class, "runtime", ApiUpdateRuntime::class );
        $moduleRegistry->register( ApiUpdateValue::class, "boolean", ApiUpdateBoolean::class );
        $moduleRegistry->register( ApiUpdateValue::class, "integer", ApiUpdateInteger::class );
        $moduleRegistry->register( ApiUpdateValue::class, "text", ApiUpdateText::class );
        $moduleRegistry->register( ApiUpdateExpression::class, "variable", ApiUpdateVariableExpression::class );
        $moduleRegistry->register( ApiUpdateExpression::class, "value", ApiUpdateValueExpression::class );
        $moduleRegistry->register( ApiUpdateExpression::class, "specificuser", ApiUpdateSpecificUserExpression::class );
        $moduleRegistry->register( ApiUpdateExpression::class, "runtimeuser", ApiUpdateRuntimeUserExpression::class );
        $moduleRegistry->register( ApiUpdateExpression::class, "comparison", ApiUpdateComparison::class );
        $moduleRegistry->register( ApiUpdateExpression::class, "conjunction", ApiUpdateConjunction::class );
        $moduleRegistry->register( ApiUpdateExpression::class, "disjunction", ApiUpdateDisjunction::class );
        $moduleRegistry->register( ApiUpdateDefinition::class, "workflow", ApiUpdateWorkflow::class );
        $moduleRegistry->register( ApiUpdateDefinition::class, "element", ApiUpdateElement::class );
        $moduleRegistry->register( ApiUpdateDefinition::class, "transition", ApiUpdateTransition::class );
        $moduleRegistry->register( ApiUpdateDefinition::class, "requiredinput", ApiUpdateRequiredInput::class );
        $moduleRegistry->register( ApiUpdateElement::class, "event", ApiUpdateEvent::class );
        $moduleRegistry->register( ApiUpdateElement::class, "parallelgateway", ApiUpdateParallelGateway::class );
        $moduleRegistry->register( ApiUpdateElement::class, "exclusivegateway", ApiUpdateExclusiveGateway::class );
        $moduleRegistry->register( ApiUpdateElement::class, "inclusivegateway", ApiUpdateInclusiveGateway::class );
        $moduleRegistry->register( ApiUpdateRuntime::class, "input", ApiUpdateInput::class );

        $moduleRegistry->register( ApiDelete::class, "value", ApiDeleteValue::class );
        $moduleRegistry->register( ApiDelete::class, "expression", ApiDeleteExpression::class );
        $moduleRegistry->register( ApiDelete::class, "definition", ApiDeleteDefinition::class );
        $moduleRegistry->register( ApiDelete::class, "runtime", ApiDeleteRuntime::class );
        $moduleRegistry->register( ApiDeleteDefinition::class, "workflow", ApiDeleteWorkflow::class );
        $moduleRegistry->register( ApiDeleteDefinition::class, "element", ApiDeleteElement::class );
        $moduleRegistry->register( ApiDeleteDefinition::class, "transition", ApiDeleteTransition::class );
        $moduleRegistry->register( ApiDeleteDefinition::class, "requiredinput", ApiDeleteRequiredInput::class );
        $moduleRegistry->register( ApiDeleteRuntime::class, "input", ApiDeleteInput::class );
        $moduleRegistry->register( ApiDeleteRuntime::class, "runtimeworkflow", ApiDeleteRuntimeWorkflow::class );

        $moduleRegistry->register( ApiExecute::class, "startworkflow", ApiExecuteStartWorkflowAction::class );
        $moduleRegistry->register( ApiExecute::class, "finishtask", ApiExecuteFinishTaskAction::class );
    }

    public static function onBeforeCreateEchoEvent(
        &$notifications, &$notificationCategories, &$icons
    ) {
        $notificationCategories[ "workflows" ] = [
            "priority" => 3,
            "tooltip" => "echo-pref-tooltip-workflows",
        ];

        $notifications[ "workflows-new-task" ] = [
            "category" => "workflows",
            "section" => "message",
            "presentation-model" => NewTaskPresentationModel::class,
            "user-locators" => [ "Workflows\\Utils::locateUsers" ],
            "bundle" => [
                "web" => true,
                "email" => true,
                "expandable" => true,
            ],
        ];

        $notifications[ "workflows-state-changed" ] = [
            "category" => "workflows",
            "section" => "message",
            "presentation-model" => StateChangedPresentationModel::class,
            "user-locators" => [ "Workflows\\Utils::locateUsers" ],
            "bundle" => [
                "web" => true,
                "email" => true,
                "expandable" => true
            ]
        ];

        $icons[ "workflows-new-task" ] = [];
        $icons[ "workflows-state-changed" ] = [];
    }

    public static function onEchoGetBundleRules( $event, &$bundleString ) : bool {
        switch ( $event->getType() ) {
            case "workflows-new-task":
                $bundleString = "workflows-new-task";
                break;
            case "workflows-state-changed":
                $bundleString = "workflows-state-changed";
                break;
        }

        return true;
    }
}