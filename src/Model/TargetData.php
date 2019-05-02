<?php

namespace AmoCrm\Model;

/**
 * Description of TargetData.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class TargetData
{
    /**
     * @var int
     */
    private $funnelId;

    /**
     * @var array
     */
    private $dealsToTargetFunnel;

    /**
     * @var array
     */
    private $resultDeals;

    /**
     * @var array
     */
    private $tasksToTarget;

    /**
     * @var array
     */
    private $resultTasks;

    /**
     * @var array
     */
    private $contactsToTarget;

    /**
     * @var array
     */
    private $resultContacts;

    /**
     * @var array
     */
    private $companiesToTarget;

    /**
     * @var array
     */
    private $resultCompanies;

    /**
     * @var array
     */
    private $customFieldsOfDeals;

    /**
     * @var array
     */
    private $customFieldsOfContacts;

    /**
     * @var array
     */
    private $customFieldsOfTasks;

    /**
     * @var array
     */
    private $customFieldsOfCompanies;

    /**
     * @var array
     */
    private $targetFunnelDeals;

    /**
     * @var array
     */
    private $notesOfDeals;

    /**
     * @var array
     */
    private $notesOfContacts;

    /**
     * @var array
     */
    private $notesOfTasks;

    /**
     * @var array
     */
    private $notesOfCompanies;

    /**
     * @var array
     */
    private $resultNotes;

    /**
     * TargetData constructor.
     */
    public function __construct()
    {
        $this->notesOfDeals = [];
        $this->notesOfContacts = [];
        $this->notesOfTasks = [];
        $this->notesOfCompanies = [];
        $this->resultNotes = [];
        $this->dealsToTargetFunnel = [];
        $this->resultDeals = [];
        $this->tasksToTarget = [];
        $this->resultTasks = [];
        $this->contactsToTarget = [];
        $this->targetFunnelDeals = [];
        $this->resultContacts = [];
        $this->companiesToTarget = [];
        $this->resultCompanies = [];
        $this->customFieldsOfDeals = [];
        $this->customFieldsOfContacts = [];
        $this->customFieldsOfTasks = [];
        $this->customFieldsOfCompanies = [];
    }

    /**
     * @return int|null
     */
    public function getFunnelId(): ?int
    {
        return $this->funnelId;
    }

    /**
     * @param int|null $funnelId
     * @return TargetData
     */
    public function setFunnelId(int $funnelId = null): self
    {
        $this->funnelId = $funnelId;

        return $this;
    }

    /**
     * @return array
     */
    public function getDealsToTargetFunnel(): array
    {
        return $this->dealsToTargetFunnel;
    }

    /**
     * @param array $dealsToTargetFunnel
     * @return TargetData
     */
    public function setDealsToTargetFunnel(array $dealsToTargetFunnel = []): self
    {
        $this->dealsToTargetFunnel = $dealsToTargetFunnel;

        return $this;
    }

    /**
     * @return array
     */
    public function getResultDeals(): array
    {
        return $this->resultDeals;
    }

    /**
     * @param array $resultDeals
     * @return TargetData
     */
    public function setResultDeals(array $resultDeals = []): self
    {
        $this->resultDeals = $resultDeals;

        return $this;
    }

    /**
     * @return array
     */
    public function getTasksToTarget(): array
    {
        return $this->tasksToTarget;
    }

    /**
     * @param array $tasksToTarget
     * @return TargetData
     */
    public function setTasksToTarget(array $tasksToTarget = []): self
    {
        $this->tasksToTarget = $tasksToTarget;

        return $this;
    }

    /**
     * @return array
     */
    public function getResultTasks(): array
    {
        return $this->resultTasks;
    }

    /**
     * @param array $resultTasks
     * @return TargetData
     */
    public function setResultTasks(array $resultTasks = []): self
    {
        $this->resultTasks = $resultTasks;

        return $this;
    }

    /**
     * @return array
     */
    public function getContactsToTarget(): array
    {
        return $this->contactsToTarget;
    }

    /**
     * @param array $contactsToTarget
     * @return TargetData
     */
    public function setContactsToTarget(array $contactsToTarget = []): self
    {
        $this->contactsToTarget = $contactsToTarget;

        return $this;
    }

    /**
     * @return array
     */
    public function getTargetFunnelDeals(): array
    {
        return $this->targetFunnelDeals;
    }

    /**
     * @param array $targetFunnelDeals
     * @return TargetData
     */
    public function setTargetFunnelDeals(array $targetFunnelDeals = []): self
    {
        $this->targetFunnelDeals = $targetFunnelDeals;

        return $this;
    }

    /**
     * @return array
     */
    public function getResultContacts(): array
    {
        return $this->resultContacts;
    }

    /**
     * @param array $resultContacts
     * @return TargetData
     */
    public function setResultContacts(array $resultContacts = []): self
    {
        $this->resultContacts = $resultContacts;

        return $this;
    }

    /**
     * @return array
     */
    public function getCompaniesToTarget(): array
    {
        return $this->companiesToTarget;
    }

    /**
     * @param array $companiesToTarget
     * @return TargetData
     */
    public function setCompaniesToTarget(array $companiesToTarget = []): self
    {
        $this->companiesToTarget = $companiesToTarget;

        return $this;
    }

    /**
     * @return array
     */
    public function getResultCompanies(): array
    {
        return $this->resultCompanies;
    }

    /**
     * @param array $resultCompanies
     * @return TargetData
     */
    public function setResultCompanies(array $resultCompanies = []): self
    {
        $this->resultCompanies = $resultCompanies;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomFieldsOfDeals(): array
    {
        return $this->customFieldsOfDeals;
    }

    /**
     * @param array $customFieldsOfDeals
     * @return TargetData
     */
    public function setCustomFieldsOfDeals(array $customFieldsOfDeals = []): self
    {
        $this->customFieldsOfDeals = $customFieldsOfDeals;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomFieldsOfContacts(): array
    {
        return $this->customFieldsOfContacts;
    }

    /**
     * @param array $customFieldsOfContacts
     * @return TargetData
     */
    public function setCustomFieldsOfContacts(array $customFieldsOfContacts = []): self
    {
        $this->customFieldsOfContacts = $customFieldsOfContacts;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomFieldsOfTasks(): array
    {
        return $this->customFieldsOfTasks;
    }

    /**
     * @param array $customFieldsOfTasks
     * @return TargetData
     */
    public function setCustomFieldsOfTasks(array $customFieldsOfTasks = []): self
    {
        $this->customFieldsOfTasks = $customFieldsOfTasks;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomFieldsOfCompanies(): array
    {
        return $this->customFieldsOfCompanies;
    }

    /**
     * @param array $customFieldsOfCompanies
     * @return TargetData
     */
    public function setCustomFieldsOfCompanies(array $customFieldsOfCompanies = []): self
    {
        $this->customFieldsOfCompanies = $customFieldsOfCompanies;

        return $this;
    }

    /**
     * @return array
     */
    public function getNotesOfDeals(): array
    {
        return $this->notesOfDeals;
    }

    /**
     * @param array $notesOfDeals
     * @return TargetData
     */
    public function setNotesOfDeals(array $notesOfDeals = []): self
    {
        $this->notesOfDeals = $notesOfDeals;

        return $this;
    }

    /**
     * @return array
     */
    public function getNotesOfContacts(): array
    {
        return $this->notesOfContacts;
    }

    /**
     * @param array $notesOfContacts
     * @return TargetData
     */
    public function setNotesOfContacts(array $notesOfContacts = []): self
    {
        $this->notesOfContacts = $notesOfContacts;

        return $this;
    }

    /**
     * @return array
     */
    public function getNotesOfTasks(): array
    {
        return $this->notesOfTasks;
    }

    /**
     * @param array $notesOfTasks
     * @return $this
     */
    public function setNotesOfTasks(array $notesOfTasks = [])
    {
        $this->notesOfTasks = $notesOfTasks;

        return $this;
    }

    /**
     * @return array
     */
    public function getNotesOfCompanies(): array
    {
        return $this->notesOfCompanies;
    }

    /**
     * @param array $notesOfCompanies
     * @return $this
     */
    public function setNotesOfCompanies(array $notesOfCompanies = [])
    {
        $this->notesOfCompanies = $notesOfCompanies;

        return $this;
    }

    /**
     * @return array
     */
    public function getResultNotes(): array
    {
        return $this->resultNotes;
    }

    /**
     * @param array $resultNotes
     * @return TargetData
     */
    public function setResultNotes(array $resultNotes  = []): self
    {
        $this->resultNotes = $resultNotes;

        return $this;
    }

}
