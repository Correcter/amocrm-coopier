<?php

namespace AmoCrm\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

/**
 * @author Vitaly Dergunov (<v.dergunov@icontext.ru>)
 */
abstract class AbstractCommands extends Command
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractCommand constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        parent::__construct();

        $this->logger = $logger;
    }
}
