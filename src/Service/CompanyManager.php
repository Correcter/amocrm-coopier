<?php

namespace AmoCrm\Service;

use Doctrine\ORM\EntityManager;

/**
 * Class CompanyManager.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class CompanyManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * DealManager constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $newDeals
     * @param array $oldCompanies
     * @return array
     */
    public static function buildCompaniesToTarget(array $newDeals = [], array $oldCompanies = []): array
    {
        $toTargetTasks = [];
        foreach ($newDeals as $oldDealId => $newDeal) {
            if (!isset($newDeal['_embedded']['items'])) {
                throw new \RuntimeException('Сделка пуста');
            }

            foreach ($newDeal['_embedded']['items'] as $deal) {
                if (!isset($oldTasks[$oldDealId])) {
                    throw new \RuntimeException('Задачи сделки пусты');
                }

                foreach ($oldTasks[$oldDealId]->getItems() as $tasks) {
                    $toTargetTasks[$deal['id']]['add'][] = [
                        'element_id' => $deal['id'] ?? 0,
                        'element_type' => $tasks['element_type'],
                        'complete_till_at' => $tasks['complete_till_at'],
                        'task_type' => $tasks['task_type'],
                        'text' => $tasks['text'],
                        'created_at' => time(),
                        'updated_at' => time(),
                        'responsible_user_id' => $tasks['responsible_user_id'],
                        'is_completed' => $tasks['is_completed'],
                        'created_by' => $tasks['created_by'],
                    ];
                }
            }
        }

        return $toTargetTasks;
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
