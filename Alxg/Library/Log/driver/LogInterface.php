<?php

namespace Alxg\Library\Log\driver;

interface LogInterface
{
    public function add($message, $level);

    public function save(string $path = null);
}