<?php

namespace AmoCrm\Command;

use AmoCrm\Exceptions\InvalidRequest;
use AmoCrm\Main;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Vitaly Dergunov (<v.dergunov@icontext.ru>)
 */
class DealBasicToTargetCommand extends AbstractCommands
{
    /**
     * @var Main
     */
    private $main;

    /**
     * DealBasicToTargetCommand constructor.
     * @param Main $main
     * @param LoggerInterface $logger
     */
    public function __construct(
        Main $main,
        LoggerInterface $logger
    ) {

        $this->main = $main;

        parent::__construct($logger);
    }

    /**
     * Configure.
     */
    public function configure()
    {
        $this
            ->setName('basic-to-target:deal')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Выполнить копирование сделки и связанных с ней сущностей в целевую воронку',
                null
            )
            ->setDescription('Загрузить сделки');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $mess = 'Список сделок актуален';
            if($this->main->copy()) {
                $mess = $this->main->getCopyResult();
            }

            $output->writeln($mess);
            $this->logger->info($mess);

            return 0;

        } catch (\RuntimeException $exc) {
            echo $exc->getMessage();
        } catch (InvalidRequest $exc) {
            $output->writeln($exc->getMessage());
        }
    }
}
