<?php

namespace AmoCrm\Request;

use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class CustomFieldsRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class CustomFieldsRequest extends AbstractRequest
{
    /**
     * CustomFieldsRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        parent::__construct($parameterBag);

        $this->setRequstUri(
            $this->parameterBag->get('requestCustom')
        );
    }
}
