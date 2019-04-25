<?php

namespace AmoCrm\Request;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class CustomerRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class CustomerRequest extends AbstractRequest
{
    /**
     * CustomerRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        parent::__construct($parameterBag);
    }

    /**
     * @param array $dealsData
     *
     * @return array
     */
    public function getCustomer(array $dealsData = []): array
    {
        $this->setRequstUri(
            $this->parameterBag->get('dealGet')
        );

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

    /**
     * @param array $params
     *
     * @return Response
     */
    public function addDeal(array $params = []): Response
    {
        return $this->dealPostRequest($params);
    }

    /**
     * @param array $dealsToUpdate
     *
     * @return Response
     */
    public function updateDealsStatuses(array $dealsToUpdate = []): Response
    {
        return $this->dealPostRequest($dealsToUpdate);
    }

    /**
     * @return DealRequest
     */
    public function clearAuth(): self
    {
        $this->clearCookie();

        return $this;
    }

    /**
     * @param array $params
     *
     * @return Response
     */
    private function dealPostRequest(array $params = []): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('dealAdd')
        );
        $this->setHttpMethod('POST');
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}