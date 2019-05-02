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
class UpdateBasicDealStatusFromTargetCommand extends AbstractCommands
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
            ->setName('target-to-basic:updateStatus')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Обновить статус базовой сделки из целевой',
                null
            )
            ->setDescription('Загрузить сделки');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $mess = 'Статусы сделок актуальны';
            if($this->main->updateStatus()) {
                $mess = 'Статусы сделок обновлены';
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
