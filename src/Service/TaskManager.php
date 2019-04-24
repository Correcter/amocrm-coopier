<?php

namespace AmoCrm\Service;

//use AmoCrm\Entity\Task;
use AmoCrm\Response\TaskResponse;
use Doctrine\ORM\EntityManager;

/**
 * Class TaskManager.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class TaskManager
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
     * @param array $replaceParams
     * @param array $dealTasks
     *
     * @return array
     */
    public static function buildTasksToTarget(array $newDeals = [], array $oldTasks = []): array
    {
        $toTargetTasks = [];
        foreach ($oldTasks as $tasks) {
            if (!($tasks instanceof TaskResponse)) {
                throw new \RuntimeException('Объект задач пуст или невалиден');
            }

            foreach ($tasks->getItems() as $task) {
                $toTargetTasks['add'][] = [
                    'element_id' => $task['element_id'],
                    'element_type' => $task['element_type'],
                    'complete_till_at' => $task['complete_till_at'],
                    'task_type' => $task['task_type'],
                    'text' => $task['text'],
                    'created_at' => time(),
                    'updated_at' => time(),
                    'responsible_user_id' => $task['responsible_user_id'],
                    'is_completed' => $task['is_completed'],
                    'created_by' => $task['created_by'],
                ], $replaceParams[$task['element_id']] ?? []);
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
