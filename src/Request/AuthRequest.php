<?php

namespace AmoCrm\Request;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

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
    }

    /**
     * @param null|string $loginType
     * @param null        $accountType
     *
     * @return Response
     */
    public function auth(string $loginType = null, $accountType = null): Response
    {
        $this->setRequstUri(
           $this->parameterBag->get('authPost')
        );
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
