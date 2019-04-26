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
     * @param array $newContacts
     *
     * @return array
     */
    public static function buildCompaniesToTarget(array $newDeals = [], array $oldCompanies = [], array $newContacts = []): array
    {
        $toTargetCompanies = [];
        foreach ($oldCompanies as $oldDealId => $companyItems) {
            foreach ($companyItems->getItems() as $company) {
                $dealIds = [];
                $contactIds = [];
                if (!isset($newDeals[$oldDealId]['_embedded']['items'])) {
                    throw new \RuntimeException('Невозможно обновить контакты. Сделка пуста');
                }

                foreach ($newDeals[$oldDealId]['_embedded']['items'] as $deal) {
                    $dealIds[] = $deal['id'];
                }

                foreach ($newContacts[$oldDealId]->getItems() as $contact) {
                    $contactIds[] = $contact['id'];
                }

                $companyTags = [];
                if (count($company['tags'])) {
                    foreach ($company['tags'] as $tag) {
                        $companyTags[] = $tag['name'];
                    }
                    $companyTags = implode(',', $companyTags);
                }

                $toTargetCompanies[$oldDealId]['add'][] = [
                    'name' => $company['name'],
                    'created_at' => $company['created_at'],
                    'updated_at' => $company['updated_at'],
                    'responsible_user_id' => $company['responsible_user_id'],
                    'created_by' => $company['created_by'],
                    'tags' => $companyTags,
                    'leads_id' => implode(',', $dealIds),
                    'customers_id' => implode(',', $company['customers']['id'] ?? []),
                    'contacts_id' => $contactIds,
                    'custom_fields' => $company['custom_fields'],
                ];
            }
        }

        return $toTargetCompanies;
    }
}
