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

        $this->setRequstUri(
            $this->parameterBag->get('requestCompany')
        );
    }


    /**
     * @param array $allDeals
     * @return CompanyResponse
     */
    public function getCompanyByUserId(array $allDeals = []): CompanyResponse
    {
        foreach ($allDeals as $deals) {
            foreach ($deals->getItems() as $deal) {

                if (!isset($deal['responsible_user_id'])) {
                    throw new \RuntimeException('Отсутствует идентификатор ответственного пользователя');
                }

                $this->setQueryParams([
                    'responsible_user_id' => $deal['responsible_user_id'],
                ]);
                $this->setHttpMethod('GET');

                $companyResult = $this->request()->getBody()->getContents();

                if (!$companyResult) {
                    continue;
                }

                return
                    new CompanyResponse(
                        \GuzzleHttp\json_decode(
                            $companyResult,
                            true,
                            JSON_UNESCAPED_UNICODE
                        )
                    );
            }
        }

        return new CompanyResponse();
    }

    /**
     * @param array $deals
     *
     * @return array
     */
    public function getCompaniesOfDeals(array $deals = []): array
    {
        $companiesOfDeal = [];
        foreach ($deals as $deal) {
            if (!isset($deal['company']['id'])) {
                continue;
            }

            $this->setQueryParams([
                'id' => (int) $deal['company']['id'],
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
}
