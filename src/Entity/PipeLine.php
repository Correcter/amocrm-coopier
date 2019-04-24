<?php

namespace AmoCrm\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PipeLine..
 *
 * @ORM\Entity(repositoryClass="AmoCrm\Repository\PipeLineRepository")
 * @ORM\Table(name="am_pipelines")
 */
class PipeLine
{
    /**
     * @ORM\OneToMany(targetEntity="AmoCrm\Entity\Deal", mappedBy="pipeline", cascade={"persist"})
     */
    protected $deals;

    /**
     * @ORM\ManyToMany(targetEntity="AmoCrm\Entity\Status", inversedBy="piplines", cascade={"persist"})
     * @ORM\JoinTable(name="am_pip_status",
     *      joinColumns={@ORM\JoinColumn(name="pipline_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="status_id", referencedColumnName="id")}
     *      )
     */
    private $statuses;

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_main", type="boolean", nullable=false)
     */
    private $isMain;

    /**
     * @var string
     *
     * @ORM\Column(name="_links", type="string")
     */
    private $_links;

    /**
     * PipeLine constructor.
     */
    public function __construct()
    {
        $this->isMain = false;
        $this->deals = new ArrayCollection();
        $this->statuses = new ArrayCollection();
    }

    /**
     * @param null|int $id
     */
    public function setId(int $id = null)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param Deal $deal
     *
     * @return PipeLine
     */
    public function addDeal(Deal $deal): self
    {
        $deal->setPipeline($this);

        if (!$this->deals->contains($deal)) {
            $this->deals[] = $deal;
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDeals(): Collection
    {
        return $this->deals;
    }

    /**
     * @param Deal $deal
     *
     * @return PipeLine
     */
    public function removeDeal(Deal $deal): self
    {
        if ($this->deals->contains($deal)) {
            $this->deals->removeElement($deal);
        }

        return $this;
    }

    /**
     * @param Status $status
     *
     * @return PipeLine
     */
    public function addStatus(Status $status): self
    {
        $status->addPipLine($this);

        if (!$this->statuses->contains($status)) {
            $this->statuses[] = $status;
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getStatuses(): Collection
    {
        return $this->statuses;
    }

    /**
     * @param Status $status
     *
     * @return PipeLine
     */
    public function removeCompany(Status $status): self
    {
        if ($this->statuses->contains($status)) {
            $this->statuses->removeElement($status);
        }

        return $this;
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
     * @return PipeLine
     */
    public function setName(string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @param null|string $sort
     *
     * @return PipeLine
     */
    public function setSort(string $sort = null): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return null|bool
     */
    public function isIsMain(): ?bool
    {
        return $this->isMain;
    }

    /**
     * @param bool $isMain
     *
     * @return PipeLine
     */
    public function setIsMain(bool $isMain = false): self
    {
        $this->isMain = $isMain;

        return $this;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return \GuzzleHttp\json_decode($this->_links);
    }

    /**
     * @param array $links
     *
     * @return PipeLine
     */
    public function setLinks(array $links = []): self
    {
        $this->_links = \GuzzleHttp\json_encode($links);

        return $this;
    }
}
