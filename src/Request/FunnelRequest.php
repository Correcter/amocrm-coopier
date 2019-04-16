<?php

namespace AmoCrm\Request;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class FunnelRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class FunnelRequest extends AbstractRequest
{
    /**
     * FunnelRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        parent::__construct($parameterBag);
    }

    /**
     * @return \GuzzleHttp\Psr7\Response
     */
    public function getFunnel()
    {
        $this->setRequstUri(
            $this->parameterBag->get('funnelGet')
        );
        $this->setHttpMethod('GET');
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        return $this->request();
    }

    /**
     * @param array $params
     * @param null  $method
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function addFunnel(array $params = [], $method = null)
    {
        $this->setRequstUri('/private/api/v2/json/pipelines/set');
        $this->setHttpMethod($method);
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}
