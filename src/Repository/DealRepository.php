<?php

namespace AmoCrm\Repository;

/**
 * Class DealRepository.
 */
class DealRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param null|int $pipeLineId
     *
     * @return mixed
     */
    public function getDealsByPipeline(int $pipeLineId = null)
    {
        return
            $this->createQueryBuilder('dl')
                ->select('dl')
                ->where('dl.pipelineId=:pipelineId')
                ->setParameter('pipelineId', $pipeLineId)
                ->getQuery()
                ->getResult();
    }
}
