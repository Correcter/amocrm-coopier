<?php

namespace AmoCrm\Request;

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
    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function leadForm(array $params = [])
    {
        $this->requestBuilder->setRequstUri(
            sprintf(
                '%1$sok/lead_ads/forms.json',
                $this->requestBuilder->getApiVer()
            )
        );
        $this->requestBuilder->setHttpMethod('GET');
        $this->requestBuilder->setQueryParams(
            array_merge([
                'fields' => 'form_id,banner_ids,campaign_ids',
//                '_campaign_id__in' => '',
//                '_campaign_id' => '',
//                '_banner_id__in' => '',
//                '_banner_id' => '',
            ], $params)
        );

        return $this->requestBuilder->request();
    }

    /**
     * @param array $params
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    public function leadInfo(array $params = [])
    {
        $params = array_merge([
            'form_id' => 0,
//            '_created_time__lt' => '',
//            '_created_time__gt' => '',
//            '_created_time__lte' => '',
//            '_created_time__gte' => '',
//            '_campaign_id__in' => '',
//            '_campaign_id' => '',
//            '_banner_id__in' => '',
//            '_banner_id' => '',
        ], $params);

        $this->requestBuilder->setHttpMethod('GET');
        $this->requestBuilder->setQueryParams($params);
        $this->requestBuilder->setRequstUri(
            sprintf(
                '%1$sok/lead_ads/%2$s.json',
                $this->requestBuilder->getApiVer(),
                $params['form_id']
            )
        );

        return $this->requestBuilder->request();
    }
}
