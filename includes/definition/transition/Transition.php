<?php

namespace Workflows\Definition\Transition;

use MiniORM\Entity;
use Workflows\Definition\Element\Element;
use Workflows\Definition\Workflow;
use Workflows\Expression\LogicalExpression;

/**
 * @Table(name: "wfs_def_transition")
 */
final class Transition extends Entity {
    /**
     * @Column(name: "source_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Definition\Element\Element")
     */
    private Element $source;

    /**
     * @Column(name: "target_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Definition\Element\Element")
     */
    private Element $target;

    /**
     * @Column(name: "logical_expr_id", type: "int", nullable: true)
     * @OneToOne(target: "Workflows\Expression\Expression")
     */
    private ?LogicalExpression $logicalExpression = null;

    /**
     * @Column(name: "workflow_id", type: "int", nullable: true)
     * @ManyToOne(target: "Workflows\Definition\Workflow")
     */
    private ?Workflow $workflow = null;

    public function __construct( Element $source, Element $target, ?LogicalExpression $logicalExpression = null ) {
        parent::__construct();
        $this->setSource( $source );
        $this->setTarget( $target );
        $this->setLogicalExpression( $logicalExpression );
    }

    public function getSource() : Element {
        return $this->source;
    }

    public function setSource( Element $source ) : void {
        $this->source = $source;
        $this->markAsDirty();
    }

    public function getTarget() : Element {
        return $this->target;
    }

    public function setTarget( Element $target ) : void {
        $this->target = $target;
        $this->markAsDirty();
    }

    public function getLogicalExpression() : ?LogicalExpression {
        return $this->logicalExpression;
    }

    public function setLogicalExpression( ?LogicalExpression $logicalExpression ) : void {
        $this->logicalExpression = $logicalExpression;
        $this->markAsDirty();
    }

    public function getWorkflow() : ?Workflow {
        return $this->workflow;
    }

    public function setWorkflow( ?Workflow $workflow ) : void {
        $this->workflow = $workflow;
        $this->markAsDirty();
    }
}