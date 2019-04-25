<?php

namespace AmoCrm\Service;

use Doctrine\ORM\EntityManager;

/**
 * Class ContactManager.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class ContactManager
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
     * @param array|null $oldContacts
     * @return array
     */
    public static function buildContactsToTarget(array $newDeals = [], array $oldContacts = []): array
    {
        $toTargetContacts = [];
        foreach ($newDeals as $oldDealId => $newDeal) {
            if (!isset($newDeal['_embedded']['items'])) {
                throw new \RuntimeException('Сделка пуста');
            }

            foreach ($newDeal['_embedded']['items'] as $deal) {
                if (!isset($oldContacts[$oldDealId])) {
                    throw new \RuntimeException('Задачи сделки пусты');
                }

                foreach ($oldContacts[$oldDealId]->getItems() as $contact) {

                    $contactTags = [];
                    if(count($contact['tags'])) {
                        foreach($contact['tags'] as $tag) {
                            $contactTags[] = $tag['name'];
                        }
                        $contactTags = implode(',', $contactTags);
                    }

                    $toTargetContacts[$deal['id']]['add'][] = [
                        'name' => $contact['name'],
                        'created_at' => $contact['created_at'],
                        'updated_at' => $contact['updated_at'],
                        'responsible_user_id' => $contact['responsible_user_id'],
                        'created_by' => $contact['created_by'],
                        'company_name' => $contact['company']['name'],
                        'updated_by' => $contact['updated_by'],
                        'tags' => $contactTags,
                        'leads_id' => $contact['leads']['id'],
                        'customers_id' => $contact['customers'],
                    ];
                }

            }
        }

        return $toTargetContacts;
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
