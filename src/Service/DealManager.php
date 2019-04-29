<?php

namespace AmoCrm\Service;

use AmoCrm\Entity\Deal;
use Doctrine\ORM\EntityManager;

/**
 * Class DealManager.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class DealManager
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
     * @param array    $deals
     * @param null|int $pipeLineId
     *
     * @return null|bool
     */
    public function writeDealsIfNotExists(array $deals = [], int $pipeLineId = null): ?bool
    {
        $dbDeals =
            $this->entityManager
                ->getRepository(\AmoCrm\Entity\Deal::class)
                ->getDealsByPipeline($pipeLineId);

        $dbIds = [];
        foreach ($dbDeals as $dbDeal) {
            if (isset($deals[$dbDeal['id']])) {
                unset($deals[$dbDeal['id']]);
            }
            unset($dbDeal);
        }

        if (0 === count($deals)) {
            return null;
        }

        $pipeLine =
            $this->entityManager
                ->getRepository(\AmoCrm\Entity\PipeLine::class)
                ->find($pipeLineId);

        foreach ($deals as $deal) {
            $dealModel = new Deal();
            $dealModel->setId($deal['id']);
            $dealModel->setName($deal['name']);
            $dealModel->setResponsibleUserId($deal['responsible_user_id']);
            $dealModel->setCreatedBy($deal['created_by']);
            $dealModel->setCreatedAt($deal['created_at']);
            $dealModel->setUpdatedAt($deal['updated_at']);
            $dealModel->setAccountId($deal['account_id']);
            $dealModel->setPipelineId($deal['pipeline_id']);
            $dealModel->setStatusId($deal['status_id']);
            $dealModel->setisDeleted($deal['is_deleted']);
            $dealModel->setMainContact($deal['main_contact']);
            $dealModel->setGroupId($deal['group_id']);
            $dealModel->setCompany($deal['company']);
            $dealModel->setClosedAt($deal['closed_at']);
            $dealModel->setClosestTaskAt($deal['closest_task_at']);
            $dealModel->setTags($deal['tags']);
            $dealModel->setCustomFields($deal['custom_fields']);
            $dealModel->setContacts($deal['contacts']);
            $dealModel->setSale($deal['sale']);
            $dealModel->setLossReasonId($deal['loss_reason_id']);
            $dealModel->setPipelineText($deal['pipeline']);
            $dealModel->setPipeline($pipeLine);
            $dealModel->setLinks($deal['_links']);
            $this->entityManager->persist($dealModel);
        }
        $this->entityManager->flush();

        return true;
    }

    public function buildCustomFields(array $deals = [])
    {
        $dealsCustomFields = [];
        foreach ($deals as $deal) {
            foreach ($deal['custom_fields'] as $customFields) {
                $dealsCustomFields[$deals['id']][] = [
                    'name' => $customFields['name'],
                ];
            }
        }
    }

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
            $toTargetDeals[$newDeal['id']]['add'][] = array_merge([
                'name' => $newDeal['name'],
                'created_at' => time(),
                'updated_at' => time(),
                'status_id' => $newDeal['status_id'],
                'responsible_user_id' => $newDeal['responsible_user_id'],
                'sale' => $newDeal['sale'],
                'tags' => $newDeal['tags'],
                'contacts_id' => $newDeal['contacts']['id'] ?? [],
                'company_id' => $newDeal['company']['id'] ?? [],
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
