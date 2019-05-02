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
    public function get(): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('funnelGet')
        );

        return parent::get();
    }

    /**
     * @param array $params
     * @return Response
     */
    public function add(array $params = []): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('funnelAdd')
        );

        return parent::add($params);
    }

    /**
     * @param null|Response $response
     * @param null|string   $funnelName
     *
     * @return null|int
     */
    public function getFunnelIdByFunnelName(Response $response = null, string $funnelName = null): ?int
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
}
