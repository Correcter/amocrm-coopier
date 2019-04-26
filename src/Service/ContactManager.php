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
     * @param array      $newDeals
     * @param null|array $oldContacts
     *
     * @return array
     */
    public static function buildContactsToTarget(array $newDeals = [], array $oldContacts = []): array
    {
        $toTargetContacts = [];

        foreach ($oldContacts  as $oldDealId => $contactItems) {
            foreach ($contactItems->getItems() as $contact) {
                $dealIds = [];
                if (!isset($newDeals[$oldDealId]['_embedded']['items'])) {
                    throw new \RuntimeException('Невозможно обновить контакты. Сделка пуста');
                }

                foreach ($newDeals[$oldDealId]['_embedded']['items'] as $deal) {
                    $dealIds[] = $deal['id'];
                }

                $contactTags = [];
                if (count($contact['tags'])) {
                    foreach ($contact['tags'] as $tag) {
                        $contactTags[] = $tag['name'];
                    }
                    $contactTags = implode(',', $contactTags);
                }

                $toTargetContacts[$oldDealId]['add'][] = [
                    'name' => $contact['name'],
                    'created_at' => $contact['created_at'],
                    'updated_at' => $contact['updated_at'],
                    'responsible_user_id' => $contact['responsible_user_id'],
                    'created_by' => $contact['created_by'],
                    'company_name' => $contact['company']['name'] ?? null,
                    'tags' => $contactTags,
                    'leads_id' => implode(',', $dealIds),
                    'customers_id' => implode(',', $contact['customers']['id'] ?? []),
                    'company_id' => $contact['company']['id'] ?? null,
                    'custom_fields' => $contact['custom_fields'],
                ];
            }
        }

        return $toTargetContacts;
    }
}
