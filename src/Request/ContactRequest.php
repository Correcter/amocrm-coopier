<?php

namespace AmoCrm\Request;

use AmoCrm\Response\ContactResponse;
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

        $limit = 100;
        $offset = 0;
        $contactsOfDeals = [];

        foreach ($deals as $deal) {
            if (!isset($deal['contacts']['id'])) {
                throw new \RuntimeException('Отсутствует идентификаторы у контактов');
            }

            $this->setQueryParams([
                'id' => $deal['contacts']['id'],
                'limit_rows' => $limit,
                'limit_offset' => ($offset * $limit),
            ]);
            $this->setHttpMethod('GET');

            $companyResult = $this->request()->getBody()->getContents();

            if (!$companyResult) {
                continue;
            }

            $contactsOfDeals[$deal['id']] =
                new ContactResponse(
                    \GuzzleHttp\json_decode(
                        $companyResult,
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
        }

        return $contactsOfDeals;
    }

    /**
     * @param array $params
     *
     * @return Response
     */
    public function addContact(array $params = []): Response
    {
        return $this->postRequest($params);
    }

    /**
     * @param array $params
     *
     * @return Response
     */
    public function updateStatuses(array $params = []): Response
    {
        return $this->postRequest($params);
    }

    /**
     * @return ContactRequest
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
            $this->parameterBag->get('contactAdd')
        );
        $this->setHttpMethod('POST');
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}
