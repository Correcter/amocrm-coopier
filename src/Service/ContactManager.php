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
     * @param string|null $operationType
     * @param array $arrayOfParams
     * @return array
     */
    public static function buildContactsToTarget(string $operationType = null, array $arrayOfParams = []): array
    {
        $toTargetContacts = [];

        if (!$operationType) {
            throw new \RuntimeException('Тип операций с контактами не указан');
        }

        if(!isset($arrayOfParams['resultDeals'])) {
            throw new \RuntimeException('Сделки для контактов не определены');
        }

        if(!isset($arrayOfParams['oldContacts'])) {
            throw new \RuntimeException('Контакты для компании не определены');
        }

        foreach ($arrayOfParams['oldContacts']  as $oldDealId => $contactItems) {
            foreach ($contactItems->getItems() as $contact) {
                $dealIds = [];
                if (!isset($arrayOfParams['resultDeals'][$oldDealId]['_embedded']['items'])) {
                    throw new \RuntimeException('Невозможно обновить контакты. Сделка пуста');
                }

                foreach ($arrayOfParams['resultDeals'][$oldDealId]['_embedded']['items'] as $deal) {
                    $dealIds[] = $deal['id'];
                }

                $contactTags = [];
                if (count($contact['tags'])) {
                    foreach ($contact['tags'] as $tag) {
                        $contactTags[] = $tag['name'];
                    }
                    $contactTags = implode(',', $contactTags);
                }

                $toTargetContacts[$oldDealId][$operationType][] = [
                    'name' => $contact['name'],
                    'created_at' => $contact['created_at'],
                    'updated_at' => $contact['updated_at'],
                    'responsible_user_id' => $contact['responsible_user_id'],
                    'created_by' => $contact['created_by'],
                    //'company_name' => $contact['company']['name'] ?? null,
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
