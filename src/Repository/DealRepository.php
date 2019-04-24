<?php

namespace AmoCrm\Repository;

/**
 * Class DealRepository.
 */
class DealRepository extends \Doctrine\ORM\EntityRepository
{
    public function countDealsByIds(array $dealIds = [])
    {
        return
            $this->createQueryBuilder('dl')
                ->select('count(dl.id)')
                ->where('dl.id IN (:dealIds)')
                ->setParameter('dealIds', $dealIds)
                ->getQuery()
                ->getSingleScalarResult();
    }

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
                ->getArrayResult();
    }
}
