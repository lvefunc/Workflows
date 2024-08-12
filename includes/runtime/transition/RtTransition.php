<?php

namespace Workflows\Runtime\Transition;

use MiniORM\Entity;
use Workflows\Expression\LogicalExpression;
use Workflows\Runtime\Element\RtElement;
use Workflows\Runtime\RtWorkflow;

/**
 * @Table(name: "wfs_rt_transition")
 */
final class RtTransition extends Entity {
    /**
     * @Column(name: "source_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Runtime\Element\RtElement")
     */
    private RtElement $source;

    /**
     * @Column(name: "target_id", type: "int", nullable: false)
     * @OneToOne(target: "Workflows\Runtime\Element\RtElement")
     */
    private RtElement $target;

    /**
     * @Column(name: "logical_expr_id", type: "int", nullable: true)
     * @OneToOne(target: "Workflows\Expression\Expression")
     */
    private ?LogicalExpression $logicalExpression;

    /**
     * @Column(name: "workflow_id", type: "int", nullable: true)
     * @ManyToOne(target: "Workflows\Runtime\RtWorkflow")
     */
    private ?RtWorkflow $workflow;

    public function __construct(
        RtElement $source,
        RtElement $target,
        ?LogicalExpression $logicalExpression = null,
        ?RtWorkflow $workflow = null
    ) {
        parent::__construct();
        $this->source = $source;
        $this->target = $target;
        $this->logicalExpression = $logicalExpression;
        $this->workflow = $workflow;
    }

    public function getSource() : RtElement {
        return $this->source;
    }

    public function getTarget() : RtElement {
        return $this->target;
    }

    public function getLogicalExpression() : ?LogicalExpression {
        return $this->logicalExpression;
    }

    public function getWorkflow() : ?RtWorkflow {
        return $this->workflow;
    }
}