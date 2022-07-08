<?php
declare(strict_types=1);

namespace SibiryakovVictor\TestEnot\SettingChangeConfirmation\Controller;

use InvalidArgumentException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\Confirmation;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Entity\ConfirmationMethod;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception\CantSendCodeByMethodException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception\ReachedLimitAttemptsConfirmException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception\ReachedLimitAttemptsSendException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Exception\TooEarlyAttemptException;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Repository\ConfirmationMethodRepository;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Repository\ConfirmationRepository;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Service\CodeCheckerInterface;
use SibiryakovVictor\TestEnot\SettingChangeConfirmation\Service\CodeSenderInterface;
use Throwable;

class SettingChangeConfirmationController
{
    const ERROR_EMPTY_USER_ID = 100;
    const ERROR_EMPTY_CONFIRMATION_METHOD = 101;
    const ERROR_EMPTY_SETTING_ID = 102;
    const ERROR_EMPTY_CODE = 103;

    const ERROR_CONFIRMATION_NOT_FOUND = 200;
    const ERROR_METHOD_NOT_FOUND = 201;

    const ERROR_REACHED_LIMIT_SEND = 300;
    const ERROR_TOO_EARLY_ATTEMPT_SEND = 301;
    const ERROR_SEND_CODE_BY_METHOD = 302;
    const ERROR_REACHED_LIMIT_CONFIRM = 303;

    const ERROR_UNEXPECTED_ERROR = 400;

    private ConfirmationMethodRepository $methodRepository;

    private ConfirmationRepository $confirmationRepository;

    private CodeSenderInterface $codeSender;

    private CodeCheckerInterface $codeChecker;

    public function __construct(
        ConfirmationMethodRepository $methodRepository,
        ConfirmationRepository $confirmationRepository,
        CodeSenderInterface $codeSender,
        CodeCheckerInterface $codeChecker
    ) {
        $this->methodRepository = $methodRepository;
        $this->confirmationRepository = $confirmationRepository;
        $this->codeSender = $codeSender;
        $this->codeChecker = $codeChecker;
    }

    /**
     * @return void
     *
     * Метод отправки кода пользователю.
     * В первом случае, когда пользователь ВПЕРВЫЕ (с момента открытия попапа подтверждения) использует
     * этот метод для данной настройки, передаются:
     * - userId (id пользователя)
     * - method (id метода)
     * - settingId (id изменяемой настройки)
     *
     * Если же попытки уже были, то передаётся только confirmationId.
     * Поэтому подразумевается, что фронт запоминает confirmationId для соответствующих методов до тех пор,
     * пока пользователь работает внутри попапа. Если пользователь открыл/закрыл попап,
     * то фронт "сбрасывает" confirmationId по методам (то есть будет передавать снова все данные по первому случаю
     * для каждого метода)
     */
    public function sendCodeAction()
    {
        try {
            $request = $this->getRequestBody();

            if (!empty($request['confirmationId'])) {
                $confirmation = $this->confirmationRepository->findById($request['confirmationId']);
                if (!$confirmation instanceof Confirmation) {
                    throw new InvalidArgumentException("confirmation is not found", self::ERROR_CONFIRMATION_NOT_FOUND);
                }
            } else {
                if (empty($request['userId'])) {
                    throw new InvalidArgumentException("empty field 'userId'", self::ERROR_EMPTY_USER_ID);
                }
                if (empty($request['method'])) {
                    throw new InvalidArgumentException("empty field 'method'", self::ERROR_EMPTY_CONFIRMATION_METHOD);
                }
                if (empty($request['settingId'])) {
                    throw new InvalidArgumentException("empty field 'settingId'", self::ERROR_EMPTY_SETTING_ID);
                }

                $method = $this->methodRepository->findById($request['method']);
                if (!$method instanceof ConfirmationMethod) {
                    throw new InvalidArgumentException("method is not found", self::ERROR_METHOD_NOT_FOUND);
                }

                $confirmation = $this->confirmationRepository->create($request['userId'], $request['settingId'], $method);
            }

            $this->codeSender->sendCode($confirmation);

            $this->sendResponse([
                'confirmationId' => $confirmation->getId()
            ]);
        } catch (ReachedLimitAttemptsSendException $exception) {
            $this->sendErrorResponse(409, self::ERROR_REACHED_LIMIT_SEND, $exception->getMessage());
        } catch (TooEarlyAttemptException $exception) {
            $this->sendErrorResponse(409, self::ERROR_TOO_EARLY_ATTEMPT_SEND, $exception->getMessage());
        } catch (CantSendCodeByMethodException $exception) {
            $this->sendErrorResponse(500, self::ERROR_SEND_CODE_BY_METHOD, $exception->getMessage());
        } catch (InvalidArgumentException $exception) {
            $this->sendErrorResponse(400, $exception->getCode(), $exception->getMessage());
        } catch (Throwable $exception) {
            $this->sendErrorResponse(500, self::ERROR_UNEXPECTED_ERROR, $exception->getMessage());
        }
    }

    /**
     * @return void
     *
     * Метод подтверждения изменения настройки. Для его использования передаются:
     * - code (введенный пользователем код в инпут)
     * - confirmationId (id подтверждения, полученный в результате вызова метода sendCodeAction)
     */
    public function confirmCodeAction()
    {
        try {
            $request = $this->getRequestBody();

            $code = $request['code'];
            if (empty($code)) {
                throw new InvalidArgumentException("empty field 'code'", self::ERROR_EMPTY_CODE);
            }
            $confirmation = $this->confirmationRepository->findById($request['confirmationId']);
            if (!$confirmation instanceof Confirmation) {
                throw new InvalidArgumentException("confirmation is not found", self::ERROR_CONFIRMATION_NOT_FOUND);
            }

            $this->codeChecker->confirmCode($confirmation, $code);

            // здесь, конечно, не очень понятно, что именно изменяет вызов $this->codeChecker->confirmCode
            // лучше бы $this->codeChecker->confirmCode возвращал какое-то DTO с данными, которые бэк должен возвращать)
            $this->sendResponse([
                'isConfirmed' => $confirmation->isConfirmed(),
                'attemptsConfirmCount' => $confirmation->getAttemptsConfirmCount()
            ]);
        } catch (ReachedLimitAttemptsConfirmException $exception) {
            $this->sendErrorResponse(409, self::ERROR_REACHED_LIMIT_CONFIRM, $exception->getMessage());
        } catch (InvalidArgumentException $exception) {
            $this->sendErrorResponse(400, $exception->getCode(), $exception->getMessage());
        } catch (Throwable $exception) {
            $this->sendErrorResponse(500, self::ERROR_UNEXPECTED_ERROR, $exception->getMessage());
        }
    }

    private function getRequestBody(): array
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    private function sendErrorResponse(int $httpStatusCode, int $errorCode, string $errorMessage)
    {
        http_response_code($httpStatusCode);
        $response = json_encode(['error' => ['code' => $errorCode, 'message' => $errorMessage]]);
        echo $response;
    }

    private function sendResponse(array $body)
    {
        http_response_code(200);
        $response = json_encode($body);
        echo $response;
    }
}