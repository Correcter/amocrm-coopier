<?php

namespace AmoCrm\Service;

/**
 * Class TaskService.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class TaskService
{
    /**
     * @param array      $newDeals
     * @param null|array $oldTasks
     *
     * @return array
     */
    public function buildTasksToTarget(array $newDeals = [], array $oldTasks = null): array
    {
        $toTargetTasks = [];
        foreach ($newDeals as $oldDealId => $newDeal) {
            if (!isset($newDeal['_embedded']['items'])) {
                throw new \RuntimeException('Сделка пуста');
            }

            foreach ($newDeal['_embedded']['items'] as $deal) {
                if (!isset($oldTasks[$oldDealId])) {
                    continue;
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
}
