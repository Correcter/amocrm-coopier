<?php

namespace AmoCrm\Exceptions;

/**
 * Class HasNoResponse.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class HasNoResponse extends InvalidRequest
{
    /**
     * HasNoResponse constructor.
     * @param string $message
     * @param int $code
     * @param InvalidRequest|null $previous
     */
    public function __construct($message = '', $code = 404, InvalidRequest $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
