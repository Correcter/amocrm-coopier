<?php

namespace AmoCrm\Request;

use GuzzleHttp\Psr7\Response;
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
     * @return Response
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
     * @param null|Response $response
     * @param null|string   $funnelName
     *
     * @return null|int
     */
    public function getFunnelIdByFunnelName(Response $response = null, string $funnelName = null)
    {
        $result = $response->getBody()->getContents();

        if (!$result) {
            return null;
        }

        $basicFunnels = new \AmoCrm\Response\DealResponse(
            \GuzzleHttp\json_decode(
                $result,
                true
            )
        );
        unset($result);

        // iConText
        foreach ($basicFunnels->getItems() as $funnel) {
            if ($funnelName === $funnel['name']) {
                return $funnel['id'];
            }
            unset($funnel);
        }

        unset($basicFunnels);

        return null;
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

    /**
     * @return FunnelRequest
     */
    public function clearAuth(): self
    {
        $this->clearCookie();

        return $this;
    }
}
