<?php

namespace AmoCrm\Response;

/**
 * Description of AuthResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class AuthResponse
{
    /**
     * @var null|bool
     */
    private $auth;

    /**
     * @var null|array
     */
    private $accounts;

    /**
     * @var null|array
     */
    private $user;

    /**
     * @var null|string
     */
    private $error;

    /**
     * @var null|int
     */
    private $errorCode;

    /**
     * @var int
     */
    private $serverTime;

    /**
     * LeadForm constructor.
     *
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        if (!isset($mapData['response'])) {
            throw new \RuntimeException('Невалидный ответ от сервера!');
        }

        foreach ($mapData['response'] as $key => $val) {
            $key = lcfirst(str_replace('_', '', ucwords($key, '_')));
            if (property_exists(__CLASS__, $key)) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * @return null|bool
     */
    public function getAuth(): bool
    {
        return $this->auth;
    }

    /**
     * @return null|array
     */
    public function getAccounts(): array
    {
        return $this->accounts;
    }

    /**
     * @return null|array
     */
    public function getUser(): array
    {
        return $this->user;
    }

    /**
     * @return null|int
     */
    public function getServerTime(): int
    {
        return $this->serverTime;
    }

    /**
     * @return null|string
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return null|int
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }
}
