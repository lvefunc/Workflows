<?php

namespace Workflows\Expression;

use MiniORM\Entity;
use Workflows\Runtime\Context\Context;

/**
 * @Table(name: "wfs_expr")
 */
abstract class Expression extends Entity {
    public abstract function evaluate( Context $context );
}