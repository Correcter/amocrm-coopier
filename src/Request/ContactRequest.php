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

        $this->setRequstUri(
            $this->parameterBag->get('requestContact')
        );
    }

    /**
     * @param array $deals
     *
     * @return array
     */
    public function getContactsOfDeals(array $deals = []): array
    {
        $contactsOfDeals = [];

        foreach ($deals as $deal) {
            if (!isset($deal['contacts']['id'])) {
                throw new \RuntimeException('Отсутствует идентификаторы у контактов');
            }

            $this->setQueryParams([
                'id' => $deal['contacts']['id'],
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
}
