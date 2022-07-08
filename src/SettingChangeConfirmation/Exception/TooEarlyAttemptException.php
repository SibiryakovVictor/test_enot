<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception;

use DateInterval;
use Exception;

class TooEarlyAttemptException extends Exception
{
    public function __construct(DateInterval $sendingWaitPeriod)
    {
        parent::__construct("trying to send code too early, try after %s seconds", $sendingWaitPeriod->format("s"));
    }
}
