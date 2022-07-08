<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Service;

use DateTime;
use Exception;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\Confirmation;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception\CantSendCodeByMethodException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception\ReachedLimitAttemptsSendException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception\TooEarlyAttemptException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Infrastructure\CodeSender\TransportCodeSenderFactory;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Repository\ConfirmationRepository;

class CodeSender implements CodeSenderInterface
{
    private TransportCodeSenderFactory $senderFactory;

    private ConfirmationRepository $repository;

    public function __construct(TransportCodeSenderFactory $senderFactory, ConfirmationRepository $repository)
    {
        $this->senderFactory = $senderFactory;
        $this->repository = $repository;
    }

    /**
     * @throws ReachedLimitAttemptsSendException
     * @throws TooEarlyAttemptException
     * @throws CantSendCodeByMethodException
     */
    public function sendCode(Confirmation $confirmation)
    {
        if ($confirmation->getAttemptsSendCount() >= $confirmation->getMethod()->getLimitAttemptsSend()) {
            throw new ReachedLimitAttemptsSendException(
                $confirmation->getMethod()->getId(),
                $confirmation->getMethod()->getLimitAttemptsSend()
            );
        }

        if ($confirmation->getLastSentDate() instanceof DateTime) {
            $nextAvailableSendingDate = $confirmation->getLastSentDate()->add($confirmation->getMethod()->getAttemptsInterval());
            $currentDate = new DateTime();
            if ($nextAvailableSendingDate > $currentDate) {
                throw new TooEarlyAttemptException($nextAvailableSendingDate->diff($currentDate));
            }
        }

        $sender = $this->senderFactory->create($confirmation->getMethod());
        try {
            $code = $sender->sendCode($confirmation->getUserId());
        } catch (Exception $exception) {
            throw new CantSendCodeByMethodException($confirmation->getMethod()->getId(), $exception->getMessage());
        }

        $confirmation->setCode($code);
        $confirmation->setLastSentDate(new DateTime());
        $confirmation->setAttemptsSendCount($confirmation->getAttemptsSendCount() + 1);
        $this->repository->update($confirmation);
    }
}
