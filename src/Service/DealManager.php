<?php

namespace AmoCrm\Service;

/**
 * Class DealManager.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class DealManager
{
    /**
     * @param array $newParams
     * @param array $basicData
     * @param array $targetData
     *
     * @return array
     */
    public static function getDealsToTarget(array $newParams = [], array $basicData = [], array $targetData = []): array
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
            $toTargetDeals['add'][] = array_merge([
                'name' => $newDeal['name'],
                'created_at' => time(),
                'updated_at' => time(),
                'status_id' => $newDeal['status_id'],
                'responsible_user_id' => $newDeal['responsible_user_id'],
                'sale' => $newDeal['sale'],
                'tags' => $newDeal['tags'],
                'contacts_id' => $newDeal['contacts']['id'],
                'company_id' => $newDeal['company']['id'],
                'custom_fields' => $newDeal['custom_fields'],
            ], $newParams);
        }

        return $toTargetDeals;
    }

    /**
     * @param array $newParams
     * @param array $icTurboDeals
     * @param array $targetRunningDealNames
     *
     * @return array
     */
    public static function updateBasicFromTargetArray(array $newParams = [], array $icTurboDeals = [], array $targetRunningDealNames = []): array
    {
        $toUpdateBasicDeals = [];
        foreach ($targetRunningDealNames as $dealName) {
            foreach ($icTurboDeals as $deal) {
                if ($deal['name'] === $dealName) {
                    $toUpdateBasicDeals['update'][] =
                    array_merge(
                        [
                        'id' => $deal['id'],
                        'updated_at' => time(),
                        'sale' => $deal['sale'],
                        'status_id' => $deal['status_id'],
                        'custom_fields' => $deal['custom_fields'],
                        ],
                        $newParams
                    );
                }
            }
        }

        return $toUpdateBasicDeals;
    }
}
