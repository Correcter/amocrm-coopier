<?php

namespace AmoCrm\Request;

use AmoCrm\Response\CompanyResponse;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class CompanyRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class CompanyRequest extends AbstractRequest
{
    /**
     * CompanyRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        parent::__construct($parameterBag);
    }

    /**
     * @param array $deals
     *
     * @return array
     */
    public function getCompaniesOfDeals(array $deals = []): array
    {
        $this->setRequstUri(
            $this->parameterBag->get('companyGet')
        );

        $limit = 100;
        $offset = 0;
        $companiesOfDeal = [];

        foreach ($deals as $deal) {
            if (!isset($deal['company']['id'])) {
                continue;
            }

            $this->setQueryParams([
                'id' => (int) $deal['company']['id'],
                'limit_rows' => $limit,
                'limit_offset' => ($offset * $limit),
            ]);
            $this->setHttpMethod('GET');

            $companyResult = $this->request()->getBody()->getContents();

            if (!$companyResult) {
                continue;
            }

            $companiesOfDeal[$deal['id']] =
                new CompanyResponse(
                    \GuzzleHttp\json_decode(
                        $companyResult,
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
        }

        return $companiesOfDeal;
    }

    /**
     * @param array $params
     *
     * @return Response
     */
    public function addCompany(array $params = []): Response
    {
        return $this->postRequest($params);
    }

    /**
     * @param array $companiesToUpdate
     *
     * @return Response
     */
    public function updateCompany(array $companiesToUpdate = []): Response
    {
        return $this->postRequest($companiesToUpdate);
    }

    /**
     * @return CompanyRequest
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
    private function postRequest(array $params = []): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('companyAdd')
        );
        $this->setHttpMethod('POST');
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}
