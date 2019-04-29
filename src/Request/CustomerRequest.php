<?php

namespace AmoCrm\Request;

use AmoCrm\Response\CustomerResponse;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class CustomerRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class CustomerRequest extends AbstractRequest
{
    /**
     * CustomerRequest constructor.
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
    public function getCustomer(array $deals = []): array
    {
        $this->setRequstUri(
            $this->parameterBag->get('customerGet')
        );

        $customerOfDeals = [];

        foreach ($deals as $deal) {
            if (!isset($deal['contacts']['id'])) {
                throw new \RuntimeException('Отсутствует идентификаторы у контактов');
            }

            $this->setQueryParams([
                'id' => $deal['contacts']['id'],
            ]);
            $this->setHttpMethod('GET');

            $customerResult = $this->request()->getBody()->getContents();

            if (!$customerResult) {
                continue;
            }

            $customerOfDeals[$deal['id']] =
                new CustomerResponse(
                    \GuzzleHttp\json_decode(
                        $customerResult,
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
        }

        return $customerOfDeals;
    }

    /**
     * @param array $params
     *
     * @return Response
     */
    public function addCustomer(array $params = []): Response
    {
        return $this->postRequest($params);
    }

    /**
     * @param array $customersToUpdate
     *
     * @return Response
     */
    public function updateCustomer(array $customersToUpdate = []): Response
    {
        return $this->postRequest($customersToUpdate);
    }

    /**
     * @return CustomerRequest
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
    private function postRequest(array $params = []): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('customerAdd')
        );
        $this->setHttpMethod('POST');
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}
