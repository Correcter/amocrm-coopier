<?php

namespace AmoCrm\Repository;

/**
 * Class PipLineRepository.
 */
class PipLineRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @return array
     */
    public function getPiplineByName(string $pipLineName = null)
    {
        return
            $this->createQueryBuilder('ag')
                ->select('ag, comp')
                ->leftJoin('ag.companies', 'comp')
                ->where('ag.agencyName = :agencyName')
                ->andWhere('ag.isSandbox = :isSandbox')
                ->setParameter('agencyName', $pipLineName)
                ->setParameter('isSandbox', false)
                ->getQuery()
                ->getOneOrNullResult();
    }
}
