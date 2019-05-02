<?php

namespace AmoCrm\Model;

/**
 * Description of BasicData.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class BasicData
{
    /**
     * @var int
     */
    private $funnelId;

    /**
     * @var array
     */
    private $socioramaDeals;

    /**
     * @var array
     */
    private $oldTasks;

    /**
     * @var array
     */
    private $oldContacts;

    /**
     * @var array
     */
    private $oldCompanies;

    /**
     * @var array
     */
    private $oldNotesOfDeals;

    /**
     * @var array
     */
    private $oldNotesOfContacts;

    /**
     * @var array
     */
    private $oldNotesOfTasks;

    /**
     * @var array
     */
    private $oldNotesOfCompanies;

    /**
     * BasicData constructor.
     */
    public function __construct()
    {
        $this->socioramaDeals  = [];
        $this->oldTasks  = [];
        $this->oldContacts  = [];
        $this->oldCompanies  = [];
        $this->oldNotesOfDeals  = [];
        $this->oldNotesOfContacts  = [];
        $this->oldNotesOfTasks  = [];
        $this->oldNotesOfCompanies = [];
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
     * @return BasicData
     */
    public function setFunnelId(int $funnelId = null): self
    {
        $this->funnelId = $funnelId;

        return $this;
    }

    /**
     * @return array
     */
    public function getSocioramaDeals(): array
    {
        return $this->socioramaDeals;
    }

    /**
     * @param array $socioramaDeals
     * @return BasicData
     */
    public function setSocioramaDeals(array $socioramaDeals): self
    {
        $this->socioramaDeals = $socioramaDeals;

        return $this;
    }

    /**
     * @return array
     */
    public function getOldTasks(): array
    {
        return $this->oldTasks;
    }

    /**
     * @param array $oldTasks
     * @return BasicData
     */
    public function setOldTasks(array $oldTasks = []): self
    {
        $this->oldTasks = $oldTasks;

        return $this;
    }

    /**
     * @return array
     */
    public function getOldContacts(): array
    {
        return $this->oldContacts;
    }

    /**
     * @param array $oldContacts
     * @return BasicData
     */
    public function setOldContacts(array $oldContacts = []): self
    {
        $this->oldContacts = $oldContacts;

        return $this;
    }

    /**
     * @return array
     */
    public function getOldCompanies(): array
    {
        return $this->oldCompanies;
    }

    /**
     * @param array $oldCompanies
     * @return BasicData
     */
    public function setOldCompanies(array $oldCompanies = []): self
    {
        $this->oldCompanies = $oldCompanies;

        return $this;
    }

    /**
     * @return array
     */
    public function getOldNotesOfDeals(): array
    {
        return $this->oldNotesOfDeals;
    }

    /**
     * @param array $oldNotesOfDeals
     * @return BasicData
     */
    public function setOldNotesOfDeals(array $oldNotesOfDeals = []): self
    {
        $this->oldNotesOfDeals = $oldNotesOfDeals;

        return $this;
    }

    /**
     * @return array
     */
    public function getOldNotesOfContacts(): array
    {
        return $this->oldNotesOfContacts;
    }

    /**
     * @param array $oldNotesOfContacts
     * @return BasicData
     */
    public function setOldNotesOfContacts(array $oldNotesOfContacts = []): self
    {
        $this->oldNotesOfContacts = $oldNotesOfContacts;

        return $this;
    }

    /**
     * @return array
     */
    public function getOldNotesOfTasks(): array
    {
        return $this->oldNotesOfTasks;
    }

    /**
     * @param array $oldNotesOfTasks
     * @return BasicData
     */
    public function setOldNotesOfTasks(array $oldNotesOfTasks = []): self
    {
        $this->oldNotesOfTasks = $oldNotesOfTasks;

        return $this;
    }

    /**
     * @return array
     */
    public function getOldNotesOfCompanies(): array
    {
        return $this->oldNotesOfCompanies;
    }

    /**
     * @param array $oldNotesOfCompanies
     * @return BasicData
     */
    public function setOldNotesOfCompanies(array $oldNotesOfCompanies = []): self
    {
        $this->oldNotesOfCompanies = $oldNotesOfCompanies;

        return $this;
    }

}
