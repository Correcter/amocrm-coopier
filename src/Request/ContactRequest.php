<?php

namespace AmoCrm\Request;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class ContactRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class ContactRequest extends AbstractRequest
{
    /**
     * DealRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        parent::__construct($parameterBag);
    }

    /**
     * @param array $deals
     *
     * @return array
     */
    public function getContactsOfDeals(array $deals = []): array
    {
        $this->setRequstUri(
            $this->parameterBag->get('contactGet')
        );



    }

    /**
     * @param array $params
     *
     * @return Response
     */
    public function addDeal(array $params = []): Response
    {
        return $this->dealPostRequest($params);
    }

    /**
     * @param array $dealsToUpdate
     *
     * @return Response
     */
    public function updateDealsStatuses(array $dealsToUpdate = []): Response
    {
        return $this->dealPostRequest($dealsToUpdate);
    }

    /**
     * @return DealRequest
     */
    public function clearAuth(): self
    {
        $this->clearCookie();

        return $this;
    }

    /**
     * @param array $params
     *
     * @return Response
     */
    private function dealPostRequest(array $params = []): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('dealAdd')
        );
        $this->setHttpMethod('POST');
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}
