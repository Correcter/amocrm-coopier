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
     * @param null|string $operationType
     * @param array       $arrayOfParams
     *
     * @return array
     */
    public static function buildCompaniesToTarget(string $operationType = null, array $arrayOfParams = []): array
    {
        if (!$operationType) {
            throw new \RuntimeException('Тип операций с компаниями не указан');
        }

        if (!isset($arrayOfParams['resultDeals'])) {
            throw new \RuntimeException('Сделки для компаний не определены');
        }

        if (!isset($arrayOfParams['oldCompanies'])) {
            throw new \RuntimeException('Старые компании не определены');
        }

        if (!isset($arrayOfParams['resultContacts'])) {
            throw new \RuntimeException('Новые контакты не определены');
        }

        $toTargetCompanies = [];
        foreach ($arrayOfParams['oldCompanies'] as $oldDealId => $companyItems) {
            foreach ($companyItems->getItems() as $company) {
                $dealIds = [];
                $contactIds = [];
                if (!isset($arrayOfParams['resultDeals'][$oldDealId]['_embedded']['items'])) {
                    throw new \RuntimeException('Невозможно обновить контакты. Сделка пуста');
                }

                foreach ($arrayOfParams['resultDeals'][$oldDealId]['_embedded']['items'] as $deal) {
                    $dealIds[] = $deal['id'];
                }

                foreach ($arrayOfParams['resultContacts'][$oldDealId]->getItems() as $contact) {
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
