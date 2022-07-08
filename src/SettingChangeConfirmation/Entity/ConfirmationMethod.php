<?php

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity;

use DateInterval;

class ConfirmationMethod
{
    /** @var string
     * Идентификатор метода подтверждения
     */
    private string $id;

    /** @var int
     * Максимально возможное количество попыток отправить код
     */
    private int $limitAttemptsSend;

    /** @var int
     * Максимально возможное количество попыток подтвердить код
     */
    private int $limitAttemptsConfirm;

    /** @var DateInterval
     * Минимальный интервал времени между отправлениями кода пользователю
     */
    private DateInterval $attemptsInterval;

    /**
     * @return int
     */
    public function getLimitAttemptsSend(): int
    {
        return $this->limitAttemptsSend;
    }

    /**
     * @return DateInterval
     */
    public function getAttemptsInterval(): DateInterval
    {
        return $this->attemptsInterval;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getLimitAttemptsConfirm(): int
    {
        return $this->limitAttemptsConfirm;
    }
}