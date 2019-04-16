<?php

namespace AmoCrm\Exceptions;

use Exception;

/**
 * Class InvalidRequest.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class InvalidRequest extends Exception
{
    public function __construct($message = '', $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
