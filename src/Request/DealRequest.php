<?php

namespace AmoCrm\Request;

use AmoCrm\Response\DealResponse;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class LeadRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class DealRequest extends AbstractRequest
{
    /**
     * DealRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        parent::__construct($parameterBag);

        $this->setRequstUri(
            $this->parameterBag->get('requestDeal')
        );
    }


    /**
     * @param array $customDeals
     * @return array
     */
    public function getDealsById(array $customDeals = []) {

        $resultDeals = [];
        foreach ($customDeals as $oldDealId => $deals) {
            foreach ($deals->getItems() as $deal) {
                if (!isset($deal['id'])) {
                    throw new \RuntimeException('Отсутствует идентификатор ответственного пользователя');
                }

                $this->setQueryParams([
                    'id' => $deal['id'],
                ]);
                $this->setHttpMethod('GET');

                $dealsResponse = $this->request()->getBody()->getContents();

                if (!$dealsResponse) {
                    continue;
                }

                $resultDeals[$oldDealId] =
                    new DealResponse(
                        \GuzzleHttp\json_decode(
                            $dealsResponse,
                            true,
                            JSON_UNESCAPED_UNICODE
                        )
                    );
            }
        }

        return $resultDeals;
    }


    /**
     * @param null|int $funnelId
     * @param null|int $statusId
     *
     * @return array
     */
    public function getDealsByFunnelId(int $funnelId = null, int $statusId = null): array
    {
        $limit = 100;
        $offset = 0;
        $actualDeals = [];

        do {
            $hasNeedle = false;
            $this->setQueryParams([
                'limit_rows' => $limit,
                'limit_offset' => ($offset * $limit),
            ]);
            $this->setHttpMethod('GET');

            $requestBody = $this->request()->getBody();

            //dump($limit, ($offset * $limit), $requestBody->getSize());

            while ($line = $requestBody->read(1000)) {
                if (false !== strpos($line, '"pipeline_id":'.$funnelId)) {
                    $hasNeedle = true;

                    break;
                }
            }

            if ($hasNeedle) {
                $dealsFilter =
                    new \AmoCrm\Response\DealResponse(
                        \GuzzleHttp\json_decode(
                            $this->request()->getBody()->getContents(),
                            true,
                            JSON_UNESCAPED_UNICODE
                        )
                    );

                if (null === $statusId) {
                    foreach ($dealsFilter->getItems() as $deal) {
                        if ($deal['pipeline_id'] === $funnelId) {
                            $actualDeals[$deal['id']] = $deal;
                        }
                    }
                } else {
                    foreach ($dealsFilter->getItems() as $deal) {
                        if ($deal['pipeline_id'] === $funnelId && $deal['status_id'] === $statusId) {
                            $actualDeals[$deal['id']] = $deal['name'];
                        }
                    }
                }
            }

            ++$offset;
        } while ($requestBody->getSize());

        return $actualDeals;
    }
}
