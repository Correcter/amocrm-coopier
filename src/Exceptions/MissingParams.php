<?php

namespace AmoCrm\Exceptions;

/**
 * Class MissingCompanies.
 */
class MissingParams extends InvalidRequest
{
    /**
     * MissingParams constructor.
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
