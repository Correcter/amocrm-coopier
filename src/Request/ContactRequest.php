<?php

namespace AmoCrm\Request;

use AmoCrm\Response\ContactResponse;
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
     * @param array $allDeals
     * @return ContactResponse
     */
    public function getContactsByUserId(array $allDeals = []): ContactResponse
    {
        foreach ($allDeals as $deals) {
            foreach ($deals->getItems() as $deal) {

                if (!isset($deal['responsible_user_id'])) {
                    throw new \RuntimeException('Отсутствует идентификатор ответственного пользователя');
                }

                $this->setQueryParams([
                    'responsible_user_id' => $deal['responsible_user_id'],
                ]);
                $this->setHttpMethod('GET');

                $contactResult = $this->request()->getBody()->getContents();

                if (!$contactResult) {
                    continue;
                }

                return
                    new ContactResponse(
                        \GuzzleHttp\json_decode(
                            $contactResult,
                            true,
                            JSON_UNESCAPED_UNICODE
                        )
                    );

            }
        }

        return new ContactResponse();
    }

    /**
     * @param array $deals
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

            $contactResult = $this->request()->getBody()->getContents();

            if (!$contactResult) {
                continue;
            }

            $contactsOfDeals[$deal['id']] =
                new ContactResponse(
                    \GuzzleHttp\json_decode(
                        $contactResult,
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
        }

        return $contactsOfDeals;
    }
}
