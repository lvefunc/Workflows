<?php

namespace Workflows\Runtime\Action;

use StatusValue;

interface IAction {
    public function execute() : StatusValue;
}