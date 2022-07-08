<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Infrastructure\CodeSender;

use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\ConfirmationMethod;

interface TransportCodeSenderFactory
{
    public function create(ConfirmationMethod $method): TransportCodeSender;
}