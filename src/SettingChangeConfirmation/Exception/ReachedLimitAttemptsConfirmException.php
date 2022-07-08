<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception;

use Exception;

class ReachedLimitAttemptsConfirmException extends Exception
{
    public function __construct(string $method, int $limit)
    {
        parent::__construct(sprintf("reached attempts limit (%d) by method '%s'", $limit, $method));
    }
}