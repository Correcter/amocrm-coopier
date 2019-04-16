<?php

namespace HandBookBundle\Command;

use AmoCrm\Exceptions\AuthError;
use AmoCrm\Exceptions\MissingParams;
use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Vitaly Dergunov (<v.dergunov@icontext.ru>)
 */
class FunnelTargetToBasicCommand extends AbstractCommand
{
    /**
     * @var AuthRequest
     */
    private $authRequest;

    /**
     * @var DealRequest
     */
    private $dealRequest;

    /**
     * @var FunnelRequest
     */
    private $funnelRequest;

    /**
     * SynсhTargetCommand constructor.
     *
     * @param AuthRequest     $authRequest
     * @param DealRequest     $dealRequest
     * @param FunnelRequest   $funnelRequest
     * @param LoggerInterface $logger
     */
    public function __construct(
        AuthRequest $authRequest,
        DealRequest $dealRequest,
        FunnelRequest $funnelRequest,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);

        $this->authRequest = $authRequest;
        $this->dealRequest = $dealRequest;
        $this->funnelRequest = $funnelRequest;
    }

    /**
     * Configure.
     */
    public function configure()
    {
        $this
            ->setName('basic-to-target:funnel')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Выполнить загрузку воронок из базового аккаунта в целевой',
                null
            )
            ->setDescription('Загрузить воронку');
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
            $this->amoBasicAuth();

            $this->logger->info('Создание отчета успешно завершено. Всего файлов: %s');
        } catch (MissingParams $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * @throws AuthError
     *
     * @return bool
     */
    private function amoBasicAuth()
    {
        $jsonResponse = $this->authRequest
            ->createClient('basicHost')
            ->auth('basicLogin', 'basicHash')
            ->getBody()
            ->getContents();

        $response = \GuzzleHttp\json_decode($jsonResponse, true);

        if ('true' !== $response['auth']) {
            throw new AuthError('Авторизация завершилась неудачей. Пожалуйста, проверьте данные формы');
        }

        $this->funnelRequest->setCookie(
            $this->authRequest->getCookie()
        );
    }

    /**
     * @param null|int $company
     *
     * @throws MissingParams
     *
     * @return bool|string
     */
    private function amoBasicFunnelSynch()
    {
        $this->funnelRequest->createClient('basicHost');
        $this->funnelRequest->getFunnel()->getBody()->getContents();
    }

    /**
     * @param null|string $companyStat
     */
    private function writeToReport(string $companyStat = null)
    {
        $this->reportManager->writeExcel($companyStat, true);
    }
}
