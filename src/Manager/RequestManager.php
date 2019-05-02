<?php

namespace AmoCrm\Manager;

use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\CompanyRequest;
use AmoCrm\Request\ContactRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use AmoCrm\Request\NoteRequest;
use AmoCrm\Request\TaskRequest;
use AmoCrm\Request\CustomFieldsRequest;
use AmoCrm\Response\TaskResponse;
use AmoCrm\Response\CustomerResponse;
use AmoCrm\Response\CompanyResponse;
use AmoCrm\Response\DealResponse;
use AmoCrm\Response\NoteResponse;
use AmoCrm\Response\CustomFieldsResponse;
use AmoCrm\Response\FunnelResponse;
use AmoCrm\Response\ContactResponse;

use AmoCrm\Exceptions\AuthError;

/**
 * Class RequestManager
 * @package AmoCrm\Manager
 */
class RequestManager extends AbstractManager {

    /**
     * @var AuthRequest
     */
    private $authRequest;

    /**
     * @var DealRequest
     */
    private $dealRequest;

    /**
     * @var FunnelRequest
     */
    private $funnelRequest;

    /**
     * @var TaskRequest
     */
    private $taskRequest;

    /**
     * @var ContactRequest
     */
    private $contactRequest;

    /**
     * @var CompanyRequest
     */
    private $companyRequest;

    /**
     * @var CustomFieldsRequest
     */
    private $customFieldsRequest;

    /**
     * @var NoteRequest
     */
    private $noteRequest;

    /**
     * RequestManager constructor.
     * @param AuthRequest $authRequest
     * @param DealRequest $dealRequest
     * @param FunnelRequest $funnelRequest
     * @param TaskRequest $taskRequest
     * @param ContactRequest $contactRequest
     * @param CompanyRequest $companyRequest
     * @param CustomFieldsRequest $customFieldsRequest
     * @param NoteRequest $noteRequest
     */
    public function __construct(
        AuthRequest $authRequest,
        DealRequest $dealRequest,
        FunnelRequest $funnelRequest,
        TaskRequest $taskRequest,
        ContactRequest $contactRequest,
        CompanyRequest $companyRequest,
        CustomFieldsRequest $customFieldsRequest,
        NoteRequest $noteRequest
    ) {
        $this->authRequest = $authRequest;
        $this->dealRequest = $dealRequest;
        $this->funnelRequest = $funnelRequest;
        $this->taskRequest = $taskRequest;
        $this->contactRequest = $contactRequest;
        $this->companyRequest = $companyRequest;
        $this->customFieldsRequest = $customFieldsRequest;
        $this->noteRequest = $noteRequest;

        parent::__construct();
    }

    /**
     * @return void
     */
    public function copyBasicDataInitialize(): void
    {
        $this->basicHostsSetUp();
        $this->amoAuth('basicLogin', 'basicHash');
        $this->cookiesSetUp();

        $funnelId = $this->getFunnelIdByFunnelName('Sociorama');
        $this->basicData->setFunnelId($funnelId);

        $this->basicData->setSocioramaDeals(
            $this->dealRequest->getDealsByFunnelId($funnelId)
        );

        $this->basicData->setOldTasks(
            $this->taskRequest->getTasksOfDeals(
                $this->basicData->getSocioramaDeals()
            )
        );

        $this->basicData->setOldContacts(
            $this->contactRequest->getContactsOfDeals(
                $this->basicData->getSocioramaDeals()
            )
        );

        $this->basicData->setOldCompanies(
            $this->companyRequest->getCompaniesOfDeals(
                $this->basicData->getSocioramaDeals()
            )
        );

        // События (примечания для всех сущностей)
        $this->basicData->setOldNotesOfDeals(
            $this->noteRequest->getNotesOfDeals(
                [
                    new \AmoCrm\Response\DealPack($this->basicData->getSocioramaDeals()),
                ]
            )
        );

        $this->basicData->setOldNotesOfContacts(
            $this->noteRequest->getNotesOfContacts(
                $this->basicData->getOldContacts()
            )
        );

        $this->basicData->setOldNotesOfTasks(
            $this->noteRequest->getNotesOfTasks(
                $this->basicData->getOldTasks()
            )
        );

        $this->basicData->setOldNotesOfCompanies(
            $this->noteRequest->getNotesOfCompanies(
                $this->basicData->getOldCompanies()
            )
        );


        dump(
            $this->basicData->getOldNotesOfDeals(),
            $this->basicData->getOldNotesOfContacts(),
            $this->basicData->getOldNotesOfTasks(),
            $this->basicData->getOldNotesOfCompanies()
        );
        exit;

    }

    /**
     * @return void
     */
    public function copyTargetDataInitialize(): void
    {

        $this->targetHostsSetUp();
        $this->amoAuth('targetLogin', 'targetHash');
        $this->cookiesSetUp();

        $funnelId = $this->getFunnelIdByFunnelName('Воронка');

        $this->targetData->setFunnelId($funnelId);
        $this->targetData->setTargetFunnelDeals(
            $this->dealRequest->getDealsByFunnelId($funnelId)
        );
    }


    /**
     * @return void
     */
    public function updateBasicDataInitialize(): void
    {

        $this->basicHostsSetUp();
        $this->amoAuth('basicLogin', 'basicHash');
        $this->cookiesSetUp();

        $funnelId = $this->getFunnelIdByFunnelName('Sociorama');

        $this->basicData->setFunnelId($funnelId);
        $this->basicData->setSocioramaDeals(
            $this->dealRequest->getDealsByFunnelId($funnelId)
        );
    }

    /**
     * @return void
     */
    public function updateTargetDataInitialize(): void
    {

        $this->targetHostsSetUp();
        $this->amoAuth('targetLogin', 'targetHash');
        $this->cookiesSetUp();

        $funnelId = $this->getFunnelIdByFunnelName('Воронка');

        $this->targetData->setFunnelId($funnelId);
        $this->targetData->setTargetFunnelDeals(
            $this->dealRequest->getDealsByFunnelId($funnelId)
        );
    }


    /**
     * @param null $login
     * @param null $hash
     * @return bool
     * @throws AuthError
     */
    private function amoAuth($login = null, $hash = null): bool
    {
        $objectResponse = new \AmoCrm\Response\AuthResponse(
            \GuzzleHttp\json_decode(
                $this->authRequest
                    ->auth($login, $hash)
                    ->getBody()
                    ->getContents(),
                true,
                JSON_UNESCAPED_UNICODE
            )
        );

        if ($objectResponse->getError()) {
            throw new AuthError($objectResponse->getError());
        }

        return true;
    }


    /**
     * @return void
     */
    public function notesRequest(): void
    {
        $resultNotes = [];
        foreach ([
                     $this->targetData->getNotesOfDeals(),
                     $this->targetData->getNotesOfTasks(),
                     $this->targetData->getNotesOfContacts(),
                     $this->targetData->getNotesOfCompanies()
                 ] as $notes) {
            foreach ($notes as $dealId => $note) {
                $resultNotes[$dealId] = new CustomFieldsResponse(
                    \GuzzleHttp\json_decode(
                        $this
                            ->noteRequest
                            ->add($note)
                            ->getBody()
                            ->getContents(),
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
            }
        }

        $this->targetData->setResultNotes($resultNotes);
        unset($resultNotes);
    }

    /**
     * @return void
     */
    public function customFieldsOfContactsRequest(): void
    {
        $customFieldsOfContacts = [];
        foreach ($this->targetData->getCustomFieldsOfContacts() as $contactId => $contact) {
            foreach ($contact as $customId => $custom) {
                $customFieldsOfContacts[$contactId][$customId] = new CustomFieldsResponse(
                    \GuzzleHttp\json_decode(
                        $this
                            ->customFieldsRequest
                            ->add($custom)
                            ->getBody()
                            ->getContents(),
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
            }
        }

        $this->targetData->setCustomFieldsOfContacts($customFieldsOfContacts);
        unset($customFieldsOfContacts);
    }


    /**
     * @return void
     */
    public function customFieldsOfCompanyRequest(): void
    {
        $customFieldsOfCompany = [];
        foreach ($this->targetData->getCustomFieldsOfCompanies() as $companyId => $company) {
            foreach ($company as $customId => $custom) {
                $customFieldsOfCompany[$companyId][$customId] = new CustomFieldsResponse(
                    \GuzzleHttp\json_decode(
                        $this
                            ->customFieldsRequest
                            ->add($custom)
                            ->getBody()
                            ->getContents(),
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
            }
        }

        $this->targetData->setCustomFieldsOfCompanies($customFieldsOfCompany);
        unset($customFieldsOfCompany);
    }


    /**
     * @return void
     */
    public function customFieldsOfDealsRequest(): void
    {
        $customFieldsOfDeals = [];
        foreach ($this->targetData->getCustomFieldsOfDeals() as $dealId => $deal) {
            foreach ($deal as $customId => $custom) {
                $customFieldsOfDeals[$dealId][$customId] = new CustomFieldsResponse(
                    \GuzzleHttp\json_decode(
                        $this
                            ->customFieldsRequest
                            ->add($custom)
                            ->getBody()
                            ->getContents(),
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
            }
        }

        $this->targetData->setCustomFieldsOfDeals($customFieldsOfDeals);
        unset($customFieldsOfDeals);
    }


    /**
     * @return void
     */
    public function dealRequest(): void
    {
        $dealsResult = [];
        foreach ($this->targetData->getDealsToTargetFunnel() as $oldDealId => $deal)
        {
            $dealsResult[$oldDealId] =
                \GuzzleHttp\json_decode(
                    $this
                        ->dealRequest
                        ->add($deal)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                );
        }

        $this->targetData->setResultDeals($dealsResult);
        unset($dealsResult);
    }

    /**
     * @return void
     */
    public function tasksRequest(): void
    {
        $newTasks = [];
        foreach ($this->targetData->getTasksToTarget() as $oldDealId => $task) {
            $newTasks[$oldDealId] = new TaskResponse(
                \GuzzleHttp\json_decode(
                    $this
                        ->taskRequest
                        ->add($task)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                )
            );
        }

        $this->targetData->setResultTasks($newTasks);
        unset($newTasks);
    }

    /**
     * @return void
     */
    public function companyRequest(): void
    {
        $newCompanies = [];
        foreach ($this->targetData->getCompaniesToTarget() as $oldDealId => $comp) {
            $companyResult =
                $this
                    ->companyRequest
                    ->add($comp)
                    ->getBody()
                    ->getContents();

            if (!$companyResult) {
                continue;
            }

            $newCompanies[$oldDealId] = new CompanyResponse(
                \GuzzleHttp\json_decode(
                    $companyResult,
                    true,
                    JSON_UNESCAPED_UNICODE
                )
            );
            unset($companyResult, $oldDealId, $comp);
        }

        $this->targetData->setResultCompanies($newCompanies);
        unset($newCompanies);
    }

    /**
     * @return void
     */
    public function contactRequest(): void
    {
        $newContacts = [];
        foreach ($this->targetData->getContactsToTarget()  as $oldDealId => $contact) {
            $newContacts[$oldDealId] = new ContactResponse(
                \GuzzleHttp\json_decode(
                    $this
                        ->contactRequest
                        ->add($contact)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                )
            );
            unset($oldDealId, $contact);
        }

        $this->targetData->setResultContacts($newContacts);
        unset($newContacts);
    }

    /**
     * @param null|string $funnelName
     *
     * @return null|int
     */
    private function getFunnelIdByFunnelName(string $funnelName = null): ?int
    {
        return
            $this->funnelRequest->getFunnelIdByFunnelName(
                $this->funnelRequest->get(),
                $funnelName
            );
    }

    /**
     * @return void
     */
    private function basicHostsSetUp(): void
    {
        $this->authRequest->createClient('basicHost');
        $this->funnelRequest->createClient('basicHost');
        $this->dealRequest->createClient('basicHost');
        $this->taskRequest->createClient('basicHost');
        $this->companyRequest->createClient('basicHost');
        $this->contactRequest->createClient('basicHost');
        $this->customFieldsRequest->createClient('basicHost');
        $this->noteRequest->createClient('basicHost');
    }

    /**
     * @return void
     */
    private function targetHostsSetUp(): void
    {
        $this->authRequest->createClient('targetHost');
        $this->funnelRequest->createClient('targetHost');
        $this->dealRequest->createClient('targetHost');
        $this->taskRequest->createClient('targetHost');
        $this->companyRequest->createClient('targetHost');
        $this->contactRequest->createClient('targetHost');
        $this->customFieldsRequest->createClient('targetHost');
        $this->noteRequest->createClient('targetHost');
    }

    /**
     * @return void
     */
    private function cookiesSetUp(): void
    {
        $this->funnelRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->dealRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->taskRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->contactRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->companyRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->noteRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->customFieldsRequest->setCookie(
            $this->authRequest->getCookie()
        );
    }

    /**
     * @return void
     */
    public function clearAuth(): void
    {
        $this->noteRequest->clearAuth();
        $this->authRequest->clearAuth();
        $this->dealRequest->clearAuth();
        $this->funnelRequest->clearAuth();
        $this->taskRequest->clearAuth();
        $this->contactRequest->clearAuth();
        $this->companyRequest->clearAuth();
        $this->customFieldsRequest->clearAuth();
    }
}