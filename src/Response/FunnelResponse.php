<?php

namespace AmoCrm\Response;

/**
 * Description of FunnelResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class FunnelResponse extends AbstractResponse
{
    /**
     * FunnelResponse constructor.
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        parent::__construct($mapData);
    }
}
