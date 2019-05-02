<?php

namespace AmoCrm\Manager;

use AmoCrm\Model\BasicData;
use AmoCrm\Model\TargetData;

/**
 * Class AbstractManager
 * @package AmoCrm\Manager
 */
class AbstractManager {
    /**
     * @var BasicData
     */
    protected $basicData;

    /**
     * @var TargetData
     */
    protected $targetData;


    /**
     * AbstractManager constructor.
     */
    public function __construct()
    {
        $this->basicData = new BasicData();
        $this->targetData = new TargetData();
    }

    /**
     * @return BasicData
     */
    protected function getBasicData(): BasicData
    {
        return $this->basicData;
    }

    /**
     * @param BasicData $basicData
     * @return AbstractManager
     */
    protected function setBasicData(BasicData $basicData = null): self
    {
        $this->basicData = $basicData;

        return $this;
    }

    /**
     * @return TargetData
     */
    protected function getTargetData(): TargetData
    {
        return $this->targetData;
    }

    /**
     * @param TargetData $targetData
     * @return AbstractManager
     */
    protected function setTargetData(TargetData $targetData = null): self
    {
        $this->targetData = $targetData;

        return $this;
    }

    /**
     * @return AbstractManager
     */
    protected function clearData(): self
    {
        $this->targetData = null;
        $this->basicData = null;

        return $this;
    }

}