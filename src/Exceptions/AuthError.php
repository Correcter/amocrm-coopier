<?php

namespace AmoCrm\Exceptions;

/**
 * Class AuthError.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class AuthError extends InvalidRequest
{
    /**
     * HasNoResponse constructor.
     *
     * @param string              $message
     * @param int                 $code
     * @param null|InvalidRequest $previous
     */
    public function __construct($message = '', $code = 404, InvalidRequest $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
