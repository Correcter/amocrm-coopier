<?php

namespace AmoCrm\Service;

use AmoCrm\Response\ContactResponse;
use AmoCrm\Response\CustomFieldsResponse;
use AmoCrm\Response\DealResponse;

/**
 * Class ContactService.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class ContactService
{
    /**
     * @param array $contacts
     *
     * @return array
     */
    public function buildCustomFields(array $contacts = []): array
    {
        $contactsCustomFields = [];
        foreach ($contacts as $contact) {
            if (!($contact instanceof ContactResponse)) {
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
                        case 'Email':
                            $fieldType = 8; // 1
                            break;
                        case 'Сайт':
                            $fieldType = 7;

                            break;
                        case 'Телефон':
                            $fieldType = 8; // 1
                            break;
                        case 'Должность':
                            $fieldType = 1;

                            break;
                        case 'Мгн. сообщения':
                            $fieldType = 8; // 1
                            break;
                        default:
                            $fieldType = 1;
                    }

                    $contactsCustomFields[$item['id']][$customFields['id']]['add'][] = [
                        'name' => $customFields['name'],
                        'field_type' => $fieldType, // MULTISELECT
                        'element_type' => 1, // Контакт
                        'origin' => md5($customFields['id'].$customFields['name']),
                        'is_editable' => true,
                        'enums' => $enums,
                    ];
                }
            }
        }

        return $contactsCustomFields;
    }

    /**
     * @param array $oldContacts
     * @param array $newCustomFields
     * @return array
     */
    public function updateCustomFields(array $oldContacts = [], array $newCustomFields = []): array
    {
        foreach ($oldContacts as $ckey => $contacts) {
            foreach ($contacts->getItems() as $cindex => $contact) {
                if (!isset($contact['custom_fields'])) {
                    continue;
                }

                foreach ($contact['custom_fields'] as $cfindex => $customFields) {
                    if (isset($newCustomFields[$contact['id']][$customFields['id']]) &&
                        $newCustomFields[$contact['id']][$customFields['id']] instanceof CustomFieldsResponse) {
                        foreach ($newCustomFields[$contact['id']][$customFields['id']]->getItems() as $item) {
                            $customFields['id'] = $item['id'];
                            $contact['custom_fields'][$cfindex] = $customFields;
                        }
                    }
                }
                $contacts->replaceCustomFields($cindex, $contact['custom_fields']);
            }
            $oldContacts[$ckey] = $contacts;
        }

        return $oldContacts;
    }

    /**
     * @param array $arrayOfParams
     *
     * @return array
     */
    public function buildContactsToTarget(array $arrayOfParams = []): array
    {
        $toTargetContacts = [];
        $customContacts = [];

        if (!isset($arrayOfParams['resultDeals'])) {
            throw new \RuntimeException('Сделки для контактов не определены');
        }

        if (!isset($arrayOfParams['oldContacts'])) {
            throw new \RuntimeException('Контакты для компании не определены');
        }

        if (!isset($arrayOfParams['allContacts'])) {
            throw new \RuntimeException('Для построения дерева контактов необходимы все имеющиеся контакты пользователя');
        }

        $contactsToUpdate =
            $this->getContactsToUpdate(
                $arrayOfParams['allContacts'],
                $arrayOfParams['oldContacts']
            );

        foreach ($arrayOfParams['oldContacts']  as $oldDealId => $contactItems) {
            foreach ($contactItems->getItems() as $contact) {
                $dealIds = [];
                $companyIds = [];
                $operationType = 'add';

                if (!isset($arrayOfParams['resultDeals'][$oldDealId]) ||
                    !($arrayOfParams['resultDeals'][$oldDealId] instanceof DealResponse)) {
                    throw new \RuntimeException('Сделки не существует или невалидный объект');
                }

                foreach ($arrayOfParams['resultDeals'][$oldDealId]->getItems() as $deal) {
                    $dealIds[] = $deal['id'];
                }

                $contactTags = [];
                if (count($contact['tags'])) {
                    foreach ($contact['tags'] as $tag) {
                        $contactTags[] = $tag['name'];
                    }
                    $contactTags = implode(',', $contactTags);
                }

                if (isset($arrayOfParams['resultCompanies'][$oldDealId])) {
                    foreach ($arrayOfParams['resultCompanies'][$oldDealId]->getItems() as $company) {
                        $companyIds[] = $company['id'];
                    }
                }

                $contactKey = $contact['name'].$contact['company']['name'] ?? null;

                if (isset($contactsToUpdate[$oldDealId][$contact['id']]) || isset($customContacts[$contactKey])) {
                    $operationType = 'update';
                }

                if ('add' === $operationType) {
                    $customContacts[$contactKey] = $contact['name'];
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
                    'company_id' => implode(',', $companyIds),
                    'custom_fields' => $contact['custom_fields'],
                ];
            }
        }

        return $toTargetContacts;
    }

    /**
     * @param null|ContactResponse $allContacts
     * @param array                $oldContacts
     *
     * @return array
     */
    private function getContactsToUpdate(ContactResponse $allContacts = null, array $oldContacts = []): array
    {
        $updateContacts = [];
        foreach ($allContacts->getItems() as $fromContact) {
            foreach ($oldContacts as $oldDealId => $oldContact) {
                foreach ($oldContact->getItems() as $toContact) {
                    $fromCompany = $fromContact['company']['name'] ?? null;
                    $toCompany = $toContact['company']['name'] ?? null;

                    if ($fromContact['name'] === $toContact['name'] &&
                        $fromCompany === $toCompany
                    ) {
                        $updateContacts[$oldDealId][$toContact['id']] = 'update';
                    }
                }
            }
        }

        return $updateContacts;
    }
}
