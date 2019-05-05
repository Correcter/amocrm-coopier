<?php

namespace AmoCrm\Response;

/**
 * Description of TaskResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class TaskResponse extends AbstractResponse
{
    /**
     * TaskResponse constructor.
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        parent::__construct($mapData);
    }
}
