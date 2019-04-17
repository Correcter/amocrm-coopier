<?php

namespace AmoCrm\Request;

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
    }

    /**
     * @return Response
     */
    public function getDeals(int $pipelineId = null): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('dealGet')
        );
        $this->setQueryParams([
            'limit_rows' => 100,
            'with' => 'name',
        ]);
        $this->setHttpMethod('GET');

        $requestBody = $this->request()->getBody();

        while (!$requestBody->eof()) {
            // Read a line from the stream
            $line = $requestBody->read(1024);
            // JSON decode the line of data
            $data = json_decode($line, true);
            dump($data);
            exit;
        }
    }

    /**
     * @param array $params
     * @param null  $method
     *
     * @return Response
     */
    public function addDeal(array $params = [], $method = null)
    {
        $this->setRequstUri('/private/api/v2/json/pipelines/set');
        $this->setHttpMethod($method);
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}
