<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Service;

use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\Confirmation;

interface CodeCheckerInterface
{
    public function confirmCode(Confirmation $confirmation, string $inputCode);
}