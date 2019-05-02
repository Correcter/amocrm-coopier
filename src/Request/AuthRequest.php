<?php

namespace AmoCrm\Request;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use AmoCrm\Exceptions\AuthError;

/**
 * Class AuthRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class AuthRequest extends AbstractRequest
{
    /**
     * AuthRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        parent::__construct($parameterBag);

        $this->setRequstUri(
            $this->parameterBag->get('requestAuth')
        );
    }

    /**
     * @param null|string $loginType
     * @param null        $accountType
     *
     * @return Response
     */
    public function auth(string $loginType = null, $accountType = null): Response
    {
        $this->setHttpMethod('POST');
        $this->setFormParams([
            'USER_LOGIN' => $this->parameterBag->get($loginType),
            'USER_HASH' => $this->parameterBag->get($accountType),
        ]);
        $this->setQueryParams([
            'type' => 'json',
        ]);

        return $this->request();
    }
}
