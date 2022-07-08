<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Service;

use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\Confirmation;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception\ReachedLimitAttemptsConfirmException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Repository\ConfirmationRepository;

class CodeChecker implements CodeCheckerInterface
{
    private ConfirmationRepository $repository;

    public function __construct(ConfirmationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws ReachedLimitAttemptsConfirmException
     */
    public function confirmCode(Confirmation $confirmation, string $inputCode)
    {
        if ($confirmation->getAttemptsConfirmCount() === 0) {
            throw new ReachedLimitAttemptsConfirmException(
                $confirmation->getMethod()->getId(),
                $confirmation->getMethod()->getLimitAttemptsConfirm()
            );
        }

        $isConfirmed = $confirmation->getCode() === $inputCode;
        if ($isConfirmed) {
            $confirmation->setIsConfirmed(true);
        } else {
            $confirmation->setAttemptsConfirmCount($confirmation->getAttemptsConfirmCount() - 1);
        }

        $this->repository->update($confirmation);
    }
}