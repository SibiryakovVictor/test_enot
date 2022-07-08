<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity;

use DateTime;

class Confirmation
{
    /** @var string
     * Идентификатор подтверждения
     */
    private string $id;

    /** @var string
     * Идентификатор пользователя
     */
    private string $userId;

    /** @var string
     * Идентификатор изменяемой настройки
     */
    private string $settingId;

    /** @var ConfirmationMethod
     * Метод подтверждения (например, telegram или SMS)
     */
    private ConfirmationMethod $method;

    /** @var string
     * Сгенерированный методом подтверждения код при отправке пользователю
     */
    private string $code;

    /** @var DateTime
     * Дата последней отправки кода
     */
    private DateTime $lastSentDate;

    /** @var bool
     * Выполнено ли подтверждение
     */
    private bool $isConfirmed;

    /** @var int
     * Число попыток подтвердить смену настройки
     */
    private int $attemptsConfirmCount;

    /** @var int
     * Число попыток выслать код пользователю
     */
    private int $attemptsSendCount;

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return ConfirmationMethod
     */
    public function getMethod(): ConfirmationMethod
    {
        return $this->method;
    }

    /**
     * @return DateTime
     */
    public function getLastSentDate(): DateTime
    {
        return $this->lastSentDate;
    }

    /**
     * @param DateTime $lastSentDate
     */
    public function setLastSentDate(DateTime $lastSentDate): void
    {
        $this->lastSentDate = $lastSentDate;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getAttemptsSendCount(): int
    {
        return $this->attemptsSendCount;
    }

    /**
     * @param int $attemptsSendCount
     */
    public function setAttemptsSendCount(int $attemptsSendCount): void
    {
        $this->attemptsSendCount = $attemptsSendCount;
    }

    /**
     * @return int
     */
    public function getAttemptsConfirmCount(): int
    {
        return $this->attemptsConfirmCount;
    }

    /**
     * @param int $attemptsConfirmCount
     */
    public function setAttemptsConfirmCount(int $attemptsConfirmCount): void
    {
        $this->attemptsConfirmCount = $attemptsConfirmCount;
    }

    /**
     * @param bool $isConfirmed
     */
    public function setIsConfirmed(bool $isConfirmed): void
    {
        $this->isConfirmed = $isConfirmed;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->isConfirmed;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}