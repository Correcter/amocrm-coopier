<?php

namespace AmoCrm\Response;

/**
 * Description of CustomFieldsResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class CustomFieldsResponse extends AbstractResponse
{
    /**
     * CustomFieldsResponse constructor.
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        parent::__construct($mapData);
    }
}
