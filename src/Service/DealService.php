<?php

namespace AmoCrm\Service;

use AmoCrm\Response\CustomFieldsResponse;

/**
 * Class DealService.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class DealService
{
    /**
     * @param array $deals
     *
     * @return array
     */
    public function buildCustomFields(array $deals = []): array
    {
        $dealsCustomFields = [];
        foreach ($deals as $deal) {
            if (!isset($deal['custom_fields'])) {
                continue;
            }

            foreach ($deal['custom_fields'] as $customFields) {
                $enums = [];
                foreach ($customFields['values'] as $value) {
                    $enums[] = $value['value'];
                }

                switch ($customFields['name']) {
                    case 'Возможный запуск':
                        $fieldType = 6;

                        break;
                    case 'Отв-ный':
                        $fieldType = 4;

                        break;
                    case 'Бюджет':
                        $fieldType = 1;

                        break;
                    case 'Услуги':
                        $fieldType = 5;

                        break;
                    default:
                        $fieldType = 1;
                }

                $dealsCustomFields[$deal['id']][$customFields['id']]['add'][] = [
                    'name' => $customFields['name'],
                    'field_type' => $fieldType,
                    'element_type' => 2, // Сделка
                    'origin' => md5($customFields['id'].$customFields['name']),
                    'is_editable' => true,
                    'enums' => $enums,
                ];
            }
        }

        return $dealsCustomFields;
    }

    /**
     * @param array $dealsToAdd
     * @param array $newCustomFields
     *
     * @return array
     */
    public function updateCustomFields(array $dealsToAdd = [], array $newCustomFields = []): array
    {
        foreach ($dealsToAdd as $dkey => $deal) {
            foreach ($deal as $operationType => $operationData) {
                foreach ($operationData as $operationDatum => $datum) {
                    if (!isset($datum['custom_fields'])) {
                        continue;
                    }

                    foreach ($datum['custom_fields'] as $fkey => $customFields) {
                        if (isset($newCustomFields[$dkey][$customFields['id']]) &&
                            $newCustomFields[$dkey][$customFields['id']] instanceof CustomFieldsResponse
                        ) {
                            foreach ($newCustomFields[$dkey][$customFields['id']]->getItems() as $item) {
                                $dealsToAdd[$dkey][$operationType][$operationDatum]['custom_fields'][$fkey]['id'] = $item['id'];
                            }
                        }
                    }
                }
            }
        }

        return $dealsToAdd;
    }

    /**
     * @param array $newParams
     * @param array $basicData
     * @param array $targetData
     *
     * @return array
     */
    public function getDealsToTarget(array $newParams = [], array $basicData = [], array $targetData = []): array
    {
        $toTargetDeals = [];
        foreach ($basicData as $basicKey => $basicDeal) {
            if (!isset($basicDeal['name'])) {
                throw new \RuntimeException('Невалидные данные массивов сделок');
            }
            foreach ($targetData as $targetDeal) {
                if ($targetDeal['name'] === $basicDeal['name']) {
                    unset($basicData[$basicKey]);
                }
            }
        }

        foreach ($basicData as $newDeal) {
            $dealsTags = [];
            if (count($newDeal['tags'])) {
                foreach ($newDeal['tags'] as $tag) {
                    $dealsTags[] = $tag['name'];
                }
                $dealsTags = implode(',', $dealsTags);
            }

            $toTargetDeals[$newDeal['id']]['add'][] = array_merge([
                'name' => $newDeal['name'],
                'created_at' => time(),
                'updated_at' => time(),
                'status_id' => $newDeal['status_id'],
                'responsible_user_id' => $newDeal['responsible_user_id'],
                'sale' => $newDeal['sale'],
                'tags' => $dealsTags,
                'contacts_id' => $newDeal['contacts']['id'] ?? [],
                'company_id' => $newDeal['company']['id'] ?? [],
                'custom_fields' => $newDeal['custom_fields'],
            ], $newParams);
        }

        return $toTargetDeals;
    }

    /**
     * @param array $basicDeals
     * @param array $targetDeals
     *
     * @return array
     */
    public function updateBasicFromTarget(array $basicDeals = [], array $targetDeals = []): array
    {
        $toUpdateBasicDeals = [];
        foreach ($basicDeals as $basicDeal) {
            foreach ($targetDeals as $targetDeal) {
                if ($basicDeal['name'] === $targetDeal['name'] &&
                    $basicDeal['status_id'] !== $targetDeal['status_id']) {
                    $toUpdateBasicDeals[$basicDeal['id']]['update'][] =
                        [
                            'id' => $basicDeal['id'],
                            'updated_at' => time(),
                            'sale' => $basicDeal['sale'],
                            'status_id' => $targetDeal['status_id'],
                            'custom_fields' => $basicDeal['custom_fields'],
                        ];
                }
            }
        }

        return $toUpdateBasicDeals;
    }

    /**
     * @param array $targetDeals
     * @param array $basicDeals
     *
     * @return array
     */
    public function updateTargetFromBasic(array $targetDeals = [], array $basicDeals = []): array
    {
        $toUpdateTargetDeals = [];
        foreach ($targetDeals as $targetDeal) {
            foreach ($basicDeals as $basicDeal) {
                if ($basicDeal['name'] === $targetDeal['name'] &&
                    $basicDeal['status_id'] !== $targetDeal['status_id']) {
                    $toUpdateTargetDeals[$targetDeal['id']]['update'][] =
                        [
                            'id' => $targetDeal['id'],
                            'updated_at' => time(),
                            'sale' => $targetDeal['sale'],
                            'status_id' => $basicDeal['status_id'],
                            'custom_fields' => $targetDeal['custom_fields'],
                        ];
                }
            }
        }

        return $toUpdateTargetDeals;
    }
}
