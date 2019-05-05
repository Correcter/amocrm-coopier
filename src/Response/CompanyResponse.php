<?php

namespace AmoCrm\Response;

/**
 * Description of CompanyResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class CompanyResponse extends AbstractResponse
{
    /**
     * CompanyResponse constructor.
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
      parent::__construct($mapData);
    }
}
