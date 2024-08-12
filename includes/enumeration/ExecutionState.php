<?php

namespace Workflows\Enumeration;

final class ExecutionState extends Enumeration {
    public const NotStarted = 0;
    public const InProgress = 1;
    public const Completed  = 2;
    public const Skipped    = 3;
    public const Obsolete   = 4;
}