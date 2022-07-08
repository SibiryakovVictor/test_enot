<?php

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Infrastructure\CodeSender;

interface TransportCodeSender
{
    public function sendCode(string $userId): string;
}