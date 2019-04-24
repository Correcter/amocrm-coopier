<?php

namespace AmoCrm\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Deal..
 *
 * @ORM\Entity(repositoryClass="AmoCrm\Repository\DealRepository")
 * @ORM\Table(name="am_deals")
 */
class Deal
{
    /**
     * @ORM\ManyToOne(targetEntity="AmoCrm\Entity\PipeLine", inversedBy="deals", cascade={"persist"})
     * @ORM\JoinColumn(name="pipeline_id", referencedColumnName="id")
     */
    private $pipeline;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var null|string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var null|int
     *
     * @ORM\Column(name="responsible_user_id", type="integer")
     */
    private $responsibleUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $createdBy;

    /**
     * @var null|int
     *
     * @ORM\Column(name="updated_at", type="integer")
     */
    private $updatedAt;

    /**
     * @var null|int
     *
     * @ORM\Column(name="created_at", type="integer")
     */
    private $createdAt;

    /**
     * @var null|int
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @var null|int
     *
     * @ORM\Column(name="pipeline_id", type="integer")
     */
    private $pipelineId;

    /**
     * @var int
     *
     * @ORM\Column(name="status_id", type="integer")
     */
    private $statusId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=false)
     */
    private $isDeleted;

    /**
     * @var string
     *
     * @ORM\Column(name="main_contact", type="string")
     */
    private $mainContact;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer")
     */
    private $groupId;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string")
     */
    private $company;

    /**
     * @var int
     *
     * @ORM\Column(name="closed_at", type="integer")
     */
    private $closedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="closest_task_at", type="integer")
     */
    private $closestTaskAt;

    /**
     * @var string
     *
     * @ORM\Column(name="tags", type="string")
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_fields", type="string")
     */
    private $customFields;

    /**
     * @var string
     *
     * @ORM\Column(name="contacts", type="string")
     */
    private $contacts;

    /**
     * @var int
     *
     * @ORM\Column(name="sale", type="integer")
     */
    private $sale;

    /**
     * @var int
     *
     * @ORM\Column(name="loss_reason_id", type="integer")
     */
    private $lossReasonId;

    /**
     * @var null|string
     *
     * @ORM\Column(name="pipeline", type="string")
     */
    private $pipelineText;

    /**
     * @var null|string
     *
     * @ORM\Column(name="_links", type="string")
     */
    private $_links;

    /**
     * Deal constructor.
     */
    public function __construct()
    {
        $this->isMain = false;
        $this->statuses = new ArrayCollection();
    }

    /**
     * @param null|int $id
     *
     * @return Deal
     */
    public function setId(int $id = null): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return Deal
     */
    public function setName(string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|bool
     */
    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    /**
     * @param bool $isDeleted
     *
     * @return Deal
     */
    public function setisDeleted(bool $isDeleted = false): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponsibleUserId(): ?string
    {
        return $this->responsibleUserId;
    }

    /**
     * @param string $responsibleUserId
     *
     * @return Deal
     */
    public function setResponsibleUserId(string $responsibleUserId): self
    {
        $this->responsibleUserId = $responsibleUserId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    /**
     * @param null|int $createdBy
     *
     * @return Deal
     */
    public function setCreatedBy(int $createdBy = null): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

    /**
     * @param null|int $updatedAt
     *
     * @return Deal
     */
    public function setUpdatedAt(int $updatedAt = null): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * @param null|int $createdAt
     *
     * @return Deal
     */
    public function setCreatedAt(int $createdAt = null): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getAccountId(): ?int
    {
        return $this->accountId;
    }

    /**
     * @param null|int $accountId
     *
     * @return Deal
     */
    public function setAccountId(int $accountId = null): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getPipelineId(): ?int
    {
        return $this->pipelineId;
    }

    /**
     * @param null|int $pipelineId
     *
     * @return Deal
     */
    public function setPipelineId(int $pipelineId = null): self
    {
        $this->pipelineId = $pipelineId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getStatusId(): ?int
    {
        return $this->statusId;
    }

    /**
     * @param null|int $statusId
     *
     * @return Deal
     */
    public function setStatusId(int $statusId = null): self
    {
        $this->statusId = $statusId;

        return $this;
    }

    /**
     * @return null|array
     */
    public function getMainContact(): array
    {
        return \GuzzleHttp\json_decode($this->mainContact);
    }

    /**
     * @param array $mainContact
     *
     * @return Deal
     */
    public function setMainContact(array $mainContact = []): self
    {
        $this->mainContact = \GuzzleHttp\json_encode($mainContact);

        return $this;
    }

    /**
     * @return null|int
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @param null|int $groupId
     *
     * @return Deal
     */
    public function setGroupId(int $groupId = null): self
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return array
     */
    public function getCompany(): array
    {
        return \GuzzleHttp\json_decode($this->company);
    }

    /**
     * @param array $company
     *
     * @return Deal
     */
    public function setCompany(array $company = []): self
    {
        $this->company = \GuzzleHttp\json_encode($company);

        return $this;
    }

    /**
     * @return null|int
     */
    public function getClosedAt(): ?int
    {
        return $this->closedAt;
    }

    /**
     * @param null|int $closedAt
     *
     * @return Deal
     */
    public function setClosedAt(int $closedAt = null): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getClosestTaskAt(): ?int
    {
        return $this->closestTaskAt;
    }

    /**
     * @param null|int $closestTaskAt
     *
     * @return Deal
     */
    public function setClosestTaskAt(int $closestTaskAt = null): self
    {
        $this->closestTaskAt = $closestTaskAt;

        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return \GuzzleHttp\json_decode($this->tags);
    }

    /**
     * @param array $tags
     *
     * @return Deal
     */
    public function setTags(array $tags = []): self
    {
        $this->tags = \GuzzleHttp\json_encode($tags);

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomFields(): array
    {
        return \GuzzleHttp\json_decode($this->customFields);
    }

    /**
     * @param array $customFields
     *
     * @return Deal
     */
    public function setCustomFields(array $customFields = []): self
    {
        $this->customFields = \GuzzleHttp\json_encode($customFields);

        return $this;
    }

    /**
     * @return array
     */
    public function getContacts(): array
    {
        return \GuzzleHttp\json_decode($this->contacts);
    }

    /**
     * @param null|array $contacts
     *
     * @return Deal
     */
    public function setContacts(array $contacts = null): self
    {
        $this->contacts = \GuzzleHttp\json_encode($contacts);

        return $this;
    }

    /**
     * @return int
     */
    public function getSale(): ?int
    {
        return $this->sale;
    }

    /**
     * @param null|int $sale
     *
     * @return Deal
     */
    public function setSale(int $sale = null): self
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * @return int
     */
    public function getLossReasonId(): int
    {
        return $this->lossReasonId;
    }

    /**
     * @param null|int $lossReasonId
     *
     * @return Deal
     */
    public function setLossReasonId(int $lossReasonId = null): self
    {
        $this->lossReasonId = $lossReasonId;

        return $this;
    }

    /**
     * @return array
     */
    public function getPipelineText(): array
    {
        return \GuzzleHttp\json_decode($this->pipelineText);
    }

    /**
     * @param array $pipelineText
     *
     * @return Deal
     */
    public function setPipelineText(array $pipelineText = []): self
    {
        $this->pipelineText = \GuzzleHttp\json_encode($pipelineText);

        return $this;
    }

    /**
     * @return null|PipeLine
     */
    public function getPipeline(): ?PipeLine
    {
        return $this->pipeline;
    }

    /**
     * @param null|PipeLine $pipeline
     *
     * @return Deal
     */
    public function setPipeline(PipeLine $pipeline = null): self
    {
        $this->pipeline = $pipeline;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinks(): array
    {
        return \GuzzleHttp\json_decode($this->_links);
    }

    /**
     * @param array $links
     *
     * @return Deal
     */
    public function setLinks(array $links = []): self
    {
        $this->_links = \GuzzleHttp\json_encode($links);

        return $this;
    }
}
