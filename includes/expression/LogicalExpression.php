<?php

namespace Workflows\Expression;

use Workflows\Runtime\Context\Context;

abstract class LogicalExpression extends Operation {
    public abstract function evaluate( Context $context ) : bool;
}