<?php

namespace AmoCrm\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Status..
 *
 * @ORM\Entity(repositoryClass="AmoCrm\Repository\StatusRepository")
 * @ORM\Table(name="am_statuses")
 */
class Status
{
    /**
     * @ORM\ManyToMany(targetEntity="AmoCrm\Entity\PipLine", mappedBy="statuses" , cascade={"persist"})
     */
    private $piplines;

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
     * @ORM\Column(name="color", type="string")
     */
    private $color;

    /**
     * @var string
     *
     * @ORM\Column(name="sort", type="string")
     */
    private $sort;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_editable", type="boolean", nullable=false)
     */
    private $isEditable;

    /**
     * Status constructor.
     */
    public function __construct()
    {
        $this->isEditable = false;
        $this->piplines = new ArrayCollection();
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
     * @param PipLine $pipline
     * @return Status
     */
    public function addPipLine(PipLine $pipline): self
    {
        $pipline->addStatus($this);

        if (!$this->piplines->contains($pipline)) {
            $this->piplines[] = $pipline;
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPipLines(): Collection
    {
        return $this->piplines;
    }

    /**
     * @param PipLine $pipLine
     * @return Status
     */
    public function removeCompany(PipLine $pipLine): self
    {
        if ($this->piplines->contains($pipLine)) {
            $this->piplines->removeElement($pipLine);
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
     * @param string|null $name
     * @return Status
     */
    public function setName(string $name = null): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getColor(): ?string
    {
        return $this->color;
    }

    /**
     * @param string|null $color
     * @return Status
     */
    public function setColor(string $color = null): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSort(): ?string
    {
        return $this->sort;
    }

    /**
     * @param string|null $sort
     * @return Status
     */
    public function setSort(string $sort = null): self
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIsEditable(): bool
    {
        return $this->isEditable;
    }

    /**
     * @param bool $isEditable
     * @return Status
     */
    public function setIsEditable(bool $isEditable = true): self
    {
        $this->isEditable = $isEditable;

        return $this;
    }

}
