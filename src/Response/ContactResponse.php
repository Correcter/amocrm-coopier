<?php

namespace AmoCrm\Response;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of ContactResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class ContactResponse extends AbstractResponse
{
    /**
     * ContactResponse constructor.
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        parent::__construct($mapData);
    }
}
