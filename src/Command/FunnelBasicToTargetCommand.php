<?php

namespace AmoCrm\Command;

use AmoCrm\Exceptions\AuthError;
use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Vitaly Dergunov (<v.dergunov@icontext.ru>)
 */
class FunnelBasicToTargetCommand extends AbstractCommands
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
            $this->authRequest->createClient('basicHost');
            $this->amoAuth('basicLogin', 'basicHash');
            $this->funnelRequest->createClient('basicHost');
            $basicFunnel = $this->amoFunnel();

            dump($basicFunnel);
            exit;

            $this->authRequest->clearAuth();

            $this->authRequest->createClient('targetHost');
            $this->amoAuth('targetLogin', 'targetHash');
            $this->funnelRequest->createClient('targetHost');
            $targetFunnel = $this->amoFunnel();

            exit;
            $this->logger->info('Создание отчета успешно завершено. Всего файлов: %s');
        } catch (\RuntimeException $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * @param null $login
     * @param null $hash
     *
     * @throws AuthError
     */
    private function amoAuth($login = null, $hash = null)
    {
        $jsonResponse =
            $this->authRequest
                ->auth($login, $hash)
                ->getBody()
                ->getContents();

        $objectResponse = new \AmoCrm\Response\AuthResponse(
            \GuzzleHttp\json_decode($jsonResponse, true)
        );

        if (true !== $objectResponse->getAuth()) {
            throw new AuthError('Авторизация завершилась неудачей. Пожалуйста, проверьте данные формы');
        }

        $this->funnelRequest->setCookie(
            $this->authRequest->getCookie()
        );
    }

    /**
     * @return array|mixed
     */
    private function amoFunnel(): array
    {
        $jsonResponse =
            $this->funnelRequest
                ->getFunnel()
                ->getBody()
                ->getContents();

        $basicFunnels = new \AmoCrm\Response\FunnelResponse(
            \GuzzleHttp\json_decode($jsonResponse, true)
        );

        foreach ($basicFunnels->getItems() as $funnel) {
            if ('iCTurbo' === $funnel['name']) {
                return $funnel;
            }
            unset($funnel);
        }

        unset($jsonResponse, $basicFunnels);

        return [];
    }
}
