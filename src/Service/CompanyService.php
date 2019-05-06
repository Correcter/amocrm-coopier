<?php

namespace AmoCrm\Service;

use AmoCrm\Response\CompanyResponse;
use AmoCrm\Response\CustomFieldsResponse;
use AmoCrm\Response\DealResponse;

/**
 * Class CompanyService.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class CompanyService
{
    /**
     * @param array $companies
     * @return array
     */
    public function buildCustomFields(array $companies = []): array
    {
        $companiesCustomFields = [];
        foreach ($companies as $company) {

            if(!($company instanceof CompanyResponse)) {
                throw new \InvalidArgumentException('Невалидный объект контактов!');
            }

            foreach ($company->getItems() as $item) {

                if(!isset($item['custom_fields'])) {
                    continue;
                }

                foreach ($item['custom_fields'] as $customFields) {

                    $enums = [];
                    foreach($customFields['values'] as $value) {
                        $enums[] = $value['value'];
                    }

                    switch($customFields['name']) {
                        case 'Телефон' :
                            $fieldType = 8; // 1
                            break;
                        case 'Email' :
                            $fieldType = 8; // 1
                            break;
                        case 'Web' :
                            $fieldType = 7;
                            break;
                        case 'Адрес' :
                            $fieldType = 1;
                            break;
                        case 'Сегмент' :
                            $fieldType = 5; // 1
                            break;
                        default:
                            $fieldType = 1;
                    }

                    $companiesCustomFields[$item['id']][$customFields['id']]['add'][] = [
                        'name' => $customFields['name'],
                        'field_type' => $fieldType,
                        'element_type' => 3, // Компания
                        'origin' => md5($customFields['id'].$customFields['name']),
                        'is_editable' => true,
                        'enums' => $enums
                    ];
                }
            }
        }

        return $companiesCustomFields;
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
     * @param CompanyResponse|null $allCompanies
     * @param array $oldCompanies
     * @return array
     */
    public function getCompaniesToUpdate(CompanyResponse $allCompanies = null, array $oldCompanies = []): array
    {
        $updateCompanies = [];
            foreach ($allCompanies->getItems() as $fromCompany) {
                foreach ($oldCompanies as $oldDealId => $oldCompany) {
                    foreach ($oldCompany->getItems() as $toCompany) {
                        if ($fromCompany['name'] === $toCompany['name']) {
                            $updateCompanies[$oldDealId][$toCompany['id']] = 'update';
                        }
                    }
                }
            }


        return $updateCompanies;
    }


    /**
     * @param array $arrayOfParams
     * @return array
     */
    public function buildCompaniesToTarget(array $arrayOfParams = []): array
    {
        if (!isset($arrayOfParams['resultDeals'])) {
            throw new \RuntimeException('Сделки для компаний не определены');
        }

        if (!isset($arrayOfParams['oldCompanies'])) {
            throw new \RuntimeException('Старые компании не определены');
        }

        if(!isset($arrayOfParams['allCompanies'])) {
            throw new \RuntimeException('Для построения дерева компаний необходимы все имеющиеся компании пользователя');
        }

        $companiesToUpdate =
            $this->getCompaniesToUpdate(
                $arrayOfParams['allCompanies'],
                $arrayOfParams['oldCompanies']
            );

        $toTargetCompanies = [];
        $customCompanies = [];

        foreach ($arrayOfParams['oldCompanies'] as $oldDealId => $companyItems) {
            foreach ($companyItems->getItems() as $company) {
                $dealIds = [];
                $contactIds = [];
                $operationType = 'add';

                if (!isset($arrayOfParams['resultDeals'][$oldDealId]) ||
                    !($arrayOfParams['resultDeals'][$oldDealId] instanceof DealResponse)) {
                    throw new \RuntimeException('Невозможно обновить контакты. Сделка пуста');
                }

                foreach ($arrayOfParams['resultDeals'][$oldDealId]->getItems() as $deal) {
                    $dealIds[] = $deal['id'];
                }

                if (isset($arrayOfParams['resultContacts'][$oldDealId])) {
                    foreach ($arrayOfParams['resultContacts'][$oldDealId]->getItems() as $contact) {
                        $contactIds[] = $contact['id'];
                    }
                }

                $companyTags = [];
                if (count($company['tags'])) {
                    foreach ($company['tags'] as $tag) {
                        $companyTags[] = $tag['name'];
                    }
                    $companyTags = implode(',', $companyTags);
                }

                if(isset($companiesToUpdate[$oldDealId][$company['id']]) || isset($customCompanies[$company['name']])) {
                    $operationType = 'update';
                }

                if ('add' === $operationType) {
                    $customCompanies[$company['name']] = $company['name'];
                }

                $toTargetCompanies[$oldDealId][$operationType][] = [
                    'name' => $company['name'],
                    'created_at' => $company['created_at'],
                    'updated_at' => $company['updated_at'],
                    'responsible_user_id' => $company['responsible_user_id'],
                    'created_by' => $company['created_by'],
                    'tags' => $companyTags,
                    'leads_id' => implode(',', $dealIds),
                    'customers_id' => implode(',', $company['customers']['id'] ?? []),
                    'contacts_id' => implode(',', $contactIds),
                    'custom_fields' => $company['custom_fields'],
                ];
            }
        }

        return $toTargetCompanies;
    }
}
