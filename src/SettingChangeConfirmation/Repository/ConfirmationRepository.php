<?php

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Repository;

use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\Confirmation;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\ConfirmationMethod;

interface ConfirmationRepository
{
    public function findById(string $confirmationId): ?Confirmation;

    public function create(string $userId, string $settingId, ConfirmationMethod $method): Confirmation;

    public function update(Confirmation $confirmation);
}
