<?php

namespace Workflows\Expression;

use User;
use Workflows\Runtime\Context\Context;

abstract class UserExpression extends Expression {
    public abstract function evaluate( Context $context ) : User;
}