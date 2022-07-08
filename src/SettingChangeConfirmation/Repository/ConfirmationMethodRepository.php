<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Repository;

use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\ConfirmationMethod;

interface ConfirmationMethodRepository
{
    public function findById(string $id): ?ConfirmationMethod;
}