<?php

namespace AmoCrm\Response;

/**
 * Description of CustomerResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class CustomerResponse extends AbstractResponse
{
    /**
     * CustomerResponse constructor.
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        parent::__construct($mapData);
    }
}
