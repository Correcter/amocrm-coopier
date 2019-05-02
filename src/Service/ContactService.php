<?php

namespace AmoCrm\Service;
use AmoCrm\Response\ContactResponse;
use AmoCrm\Response\CustomFieldsResponse;

/**
 * Class ContactService.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class ContactService
{
    /**
     * @param array $contacts
     * @return array
     */
    public function buildCustomFields(array $contacts = []): array
    {

        $contactsCustomFields = [];
        foreach ($contacts as $contact) {

            if(!($contact instanceof ContactResponse)) {
                throw new \InvalidArgumentException('Невалидный объект контактов!');
            }

            foreach ($contact->getItems() as $item) {

                if (!isset($item['custom_fields'])) {
                    continue;
                }

                foreach ($item['custom_fields'] as $customFields) {

                    $enums = [];
                    foreach ($customFields['values'] as $value) {
                        $enums[] = $value['value'];
                    }

                    switch ($customFields['name']) {
                        case 'Email' :
                            $fieldType = 8; // 1
                            break;
                        case 'Сайт' :
                            $fieldType = 7;
                            break;
                        case 'Телефон' :
                            $fieldType = 8; // 1
                            break;
                        case 'Должность' :
                            $fieldType = 1;
                            break;
                        case 'Мгн. сообщения' :
                            $fieldType = 8; // 1
                            break;
                        default:
                            $fieldType = 1;
                    }

                    $contactsCustomFields[$item['id']][$customFields['id']]['add'][] = [
                        'name' => $customFields['name'],
                        'field_type' => $fieldType, // MULTISELECT
                        'element_type' => 1, // Контакт
                        'origin' => md5($customFields['id'] . $customFields['name']),
                        'is_editable' => true,
                        'enums' => $enums
                    ];
                }
            }
        }

        return $contactsCustomFields;
    }

    /**
     * @param array $deals
     * @param array $newCustomFields
     * @return array
     */
    public function updateCustomFields(array $deals = [], array $newCustomFields = []): array
    {
        foreach ($deals as $dkey => $contacts) {
            foreach ($contacts->getItems() as $ckey => $contact) {

                if(!isset($contact['custom_fields'])) {
                    continue;
                }

                foreach ($contact['custom_fields'] as $customFields) {

                    if(isset($newCustomFields[$dkey][$customFields['id']]) &&
                        $newCustomFields[$dkey][$customFields['id']] instanceof CustomFieldsResponse) {

                        foreach($newCustomFields[$dkey][$customFields['id']]->getItems() as $item) {
                            $customFields['id'] = $item['id'];
                            $contacts->replaceCustomFields($ckey, $customFields);
                        }
                    }
                }
            }
            $deals[$dkey] = $contacts;
        }

        return $deals;
    }

    /**
     * @param string|null $operationType
     * @param array $arrayOfParams
     * @return array
     */
    public function buildContactsToTarget(string $operationType = null, array $arrayOfParams = []): array
    {
        $toTargetContacts = [];

        if (!$operationType) {
            throw new \RuntimeException('Тип операций с контактами не указан');
        }

        if(!isset($arrayOfParams['resultDeals'])) {
            throw new \RuntimeException('Сделки для контактов не определены');
        }

        if(!isset($arrayOfParams['oldContacts'])) {
            throw new \RuntimeException('Контакты для компании не определены');
        }

        foreach ($arrayOfParams['oldContacts']  as $oldDealId => $contactItems) {
            foreach ($contactItems->getItems() as $contact) {
                $dealIds = [];
                if (!isset($arrayOfParams['resultDeals'][$oldDealId]['_embedded']['items'])) {
                    throw new \RuntimeException('Невозможно обновить контакты. Сделка пуста');
                }

                foreach ($arrayOfParams['resultDeals'][$oldDealId]['_embedded']['items'] as $deal) {
                    $dealIds[] = $deal['id'];
                }

                $contactTags = [];
                if (count($contact['tags'])) {
                    foreach ($contact['tags'] as $tag) {
                        $contactTags[] = $tag['name'];
                    }
                    $contactTags = implode(',', $contactTags);
                }

                if($arrayOfParams['resultCompanies']) {
                    dump($arrayOfParams['resultCompanies']);

                }


                $toTargetContacts[$oldDealId][$operationType][] = [
                    'name' => $contact['name'],
                    'created_at' => $contact['created_at'],
                    'updated_at' => $contact['updated_at'],
                    'responsible_user_id' => $contact['responsible_user_id'],
                    'created_by' => $contact['created_by'],
                    //'company_name' => $contact['company']['name'] ?? null,
                    'tags' => $contactTags,
                    'leads_id' => implode(',', $dealIds),
                    'customers_id' => implode(',', $contact['customers']['id'] ?? []),
                    'company_id' => $contact['company']['id'] ?? null,
                    'custom_fields' => $contact['custom_fields'],
                ];
            }
        }

        return $toTargetContacts;
    }
}
