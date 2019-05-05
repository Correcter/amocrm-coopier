<?php

namespace AmoCrm\Response;

/**
 * Description of NoteResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class NoteResponse extends AbstractResponse
{
    /**
     * NoteResponse constructor.
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        parent::__construct($mapData);
    }
}
