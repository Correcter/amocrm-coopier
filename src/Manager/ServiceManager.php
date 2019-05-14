<?php

namespace AmoCrm\Manager;

use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\CompanyRequest;
use AmoCrm\Request\ContactRequest;
use AmoCrm\Request\CustomFieldsRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use AmoCrm\Request\NoteRequest;
use AmoCrm\Request\TaskRequest;
use AmoCrm\Service\CompanyService;
use AmoCrm\Service\ContactService;
use AmoCrm\Service\DealService;
use AmoCrm\Service\NoteService;
use AmoCrm\Service\TaskService;

/**
 * Class ServiceManager.
 */
class ServiceManager extends RequestManager
{
    /**
     * @var DealService
     */
    private $dealService;

    /**
     * @var CompanyService
     */
    private $companyService;

    /**
     * @var ContactService
     */
    private $contactService;

    /**
     * @var TaskService
     */
    private $taskService;

    /**
     * @var NoteService
     */
    private $noteService;

    /**
     * ServiceManager constructor.
     *
     * @param DealService         $dealService
     * @param CompanyService      $companyService
     * @param ContactService      $contactService
     * @param TaskService         $taskService
     * @param NoteService         $noteService
     * @param AuthRequest         $authRequest
     * @param DealRequest         $dealRequest
     * @param FunnelRequest       $funnelRequest
     * @param TaskRequest         $taskRequest
     * @param ContactRequest      $contactRequest
     * @param CompanyRequest      $companyRequest
     * @param CustomFieldsRequest $customFieldsRequest
     * @param NoteRequest         $noteRequest
     */
    public function __construct(
        DealService $dealService,
        CompanyService $companyService,
        ContactService $contactService,
        TaskService $taskService,
        NoteService $noteService,
        AuthRequest $authRequest,
        DealRequest $dealRequest,
        FunnelRequest $funnelRequest,
        TaskRequest $taskRequest,
        ContactRequest $contactRequest,
        CompanyRequest $companyRequest,
        CustomFieldsRequest $customFieldsRequest,
        NoteRequest $noteRequest
    ) {
        $this->dealService = $dealService;
        $this->companyService = $companyService;
        $this->contactService = $contactService;
        $this->taskService = $taskService;
        $this->noteService = $noteService;

        parent::__construct(
            $authRequest,
            $dealRequest,
            $funnelRequest,
            $taskRequest,
            $contactRequest,
            $companyRequest,
            $customFieldsRequest,
            $noteRequest
        );
    }

    /**
     * @return bool
     */
    public function ifNeedToAdd(): bool
    {
        $dealsToTargetFunnel =
            $this->dealService->getDealsToTarget(
                [
                    'pipeline_id' => $this->targetData->getFunnelId(),
                ],
                $this->basicData->getSocioramaDeals(),
                $this->targetData->getTargetFunnelDeals()
            );

        if (!$dealsToTargetFunnel) {
            return false;
        }

        $this->targetData->setDealsToTargetFunnel($dealsToTargetFunnel);
        unset($dealsToTargetFunnel);

        return true;
    }

    /**
     * @return ServiceManager
     */
    public function buildTasksToTarget(): self
    {
        $this->targetData->setTasksToTarget(
            $this->taskService->buildTasksToTarget(
                $this->targetData->getResultDeals(),
                $this->basicData->getOldTasks()
            )
        );

        return $this;
    }

    /**
     * @param string|null $operationType
     * @return ServiceManager
     */
    public function buildNotesToTarget(string $operationType = null): self
    {
        // Собираем дополнительные поля сделок
        $this->targetData->setNotesOfDeals(
            $this->noteService->buildNotesToTarget(
                $this->targetData->getResultDeals(),
                $this->basicData->getOldNotesOfDeals(),
                $operationType
            )
        );

        // Собираем дополнительные поля задач
        $this->targetData->setNotesOfTasks(
            $this->noteService->buildNotesToTarget(
                $this->targetData->getResultTasks(),
                $this->basicData->getOldNotesOfTasks(),
                $operationType
            )
        );

        // Собираем дополнительные поля контактов
        $this->targetData->setNotesOfContacts(
            $this->noteService->buildNotesToTarget(
                $this->targetData->getResultContacts(),
                $this->basicData->getOldNotesOfContacts(),
                $operationType
            )
        );

        // Собираем дополнительные поля компаний
        $this->targetData->setNotesOfCompanies(
            $this->noteService->buildNotesToTarget(
                $this->targetData->getResultCompanies(),
                $this->basicData->getOldNotesOfCompanies(),
                $operationType
            )
        );

        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function buildContactsToTarget(): self
    {
        $this->targetData->setContactsToTarget(
            $this->contactService->buildContactsToTarget(
                [
                    'resultDeals' => $this->targetData->getResultDeals(),
                    'oldContacts' => $this->basicData->getOldContacts(),
                    'resultCompanies' => $this->targetData->getResultCompanies(),
                    'allContacts' => $this->basicData->getAllContacts(),
                ]
            )
        );

        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function buildCompaniesToTarget(): self
    {
        $this->targetData->setCompaniesToTarget(
            $this->companyService->buildCompaniesToTarget(
                [
                    'resultDeals' => $this->targetData->getResultDeals(),
                    'oldCompanies' => $this->basicData->getOldCompanies(),
                    'resultContacts' => $this->targetData->getResultContacts(),
                    'allCompanies' => $this->basicData->getAllCompanies(),
                ]
            )
        );

        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function buildCustomFields(): self
    {
        // Собираем дополнительные поля сделок
        $this->targetData->setCustomFieldsOfDeals(
            $this->dealService->buildCustomFields(
                $this->basicData->getSocioramaDeals()
            )
        );

        // Собираем дополнительные поля контактов
        $this->targetData->setCustomFieldsOfContacts(
            $this->contactService->buildCustomFields(
                $this->basicData->getOldContacts()
            )
        );

        // Собираем дополнительные поля компаний
        $this->targetData->setCustomFieldsOfCompanies(
            $this->companyService->buildCustomFields(
                $this->basicData->getOldCompanies()
            )
        );

        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function updateCustomFields(): self
    {
        // Обновим дополнительные поля сделок
        $this->targetData->setDealsToTargetFunnel(
            $this->dealService->updateCustomFields(
                $this->targetData->getDealsToTargetFunnel(),
                $this->targetData->getCustomFieldsOfDeals()
            )
        );

        // Собираем дополнительные поля контактов
        $this->targetData->setContactsToTarget(
            $this->contactService->updateCustomFields(
                $this->basicData->getOldContacts(),
                $this->targetData->getCustomFieldsOfContacts()
            )
        );

        // Собираем дополнительные поля компаний
        $this->targetData->setCompaniesToTarget(
            $this->companyService->updateCustomFields(
                $this->basicData->getOldCompanies(),
                $this->targetData->getCustomFieldsOfCompanies()
            )
        );

        return $this;
    }

    /**
     * UPDATE OPERATIONS.
     *
     * @return ServiceManager
     */
    public function buildBasicFromTargetStatuses(): self
    {
        $this->targetData->setDealsToTargetFunnel(
            $this->dealService->updateBasicFromTarget(
                $this->basicData->getSocioramaDeals(),
                $this->targetData->getTargetFunnelDeals()
            )
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function hasDealsToUpdate(): bool
    {
        return 0 === count($this->targetData->getDealsToTargetFunnel()) ? false : true;
    }

    /**
     * @return string
     */
    public function buildCopyStat(): string
    {
        $mess = 'Добавлено сделок: '.count($this->targetData->getResultDeals())."\n";
        $mess .= 'Добавлено задач: '.count($this->targetData->getResultTasks())."\n";
        $mess .= 'Добавлено компаний: '.count($this->targetData->getResultCompanies())."\n";
        $mess .= 'Добавлено контактов: '.count($this->targetData->getResultContacts())."\n";

        return $mess;
    }
}
