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
     * @param array $basicData
     * @param array $targetData
     *
     * @return array
     */
    public static function getDealsToTarget(array $basicData = [], array $targetData = []): array
    {
        $toTargetDeals = [];
        foreach ($basicData as &$basicDeal) {
            if (!isset($basicDeal['name'])) {
                throw new \RuntimeException('Невалидные данные массивов сделок');
            }
            foreach ($targetData as &$targetDeal) {
                if ($targetDeal['name'] === $basicDeal['name']) {
                    unset($basicDeal);
                }
            }
        }

        foreach ($basicData as $newDeal) {
            $toTargetDeals['add'][] = [
                'name' => $newDeal['name'],
                'created_at' => time(),
                'updated_at' => time(),
                'status_id' => $newDeal['status_id'],
                'responsible_user_id' => $newDeal['responsible_user_id'],
                'sale' => $newDeal['sale'],




                
            ];
        }

        return $basicData;
    }
}
