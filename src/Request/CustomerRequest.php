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

        $this->setRequstUri(
            $this->parameterBag->get('requestCustomer')
        );
    }

    /**
     * @param array $deals
     *
     * @return array
     */
    public function getCustomer(array $deals = []): array
    {
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
}
