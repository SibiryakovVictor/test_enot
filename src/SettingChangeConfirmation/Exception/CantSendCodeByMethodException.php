<?php

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception;

use Exception;

class CantSendCodeByMethodException extends Exception
{
    public function __construct(string $method, string $message)
    {
        parent::__construct(sprintf("can't send code through %s: %s", $method, $message));
    }
}