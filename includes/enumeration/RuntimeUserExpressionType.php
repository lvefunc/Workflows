<?php

namespace Workflows\Enumeration;

final class RuntimeUserExpressionType extends Enumeration {
    public const Owner  = "Workflow owner";
    public const Random = "Random user";
    public const Sysop  = "Sysop";
}