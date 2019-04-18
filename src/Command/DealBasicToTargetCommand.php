<?php

namespace AmoCrm\Command;

use AmoCrm\Exceptions\AuthError;
use AmoCrm\Exceptions\MissingParams;
use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use AmoCrm\Service\DealManager;
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
            ->setName('basic-to-target:deal')
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Выполнить копирование сделки в целевую воронку',
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
            $this->authRequest->createClient('basicHost');
            $this->amoAuth('basicLogin', 'basicHash');

            $this->funnelRequest->createClient('basicHost');
            $funnelId = $this->getFunnelIdByFunnelName('iCTurbo');

            $this->dealRequest->createClient('basicHost');
            $icTurboDeals = $this->getDealsByFunnelId($funnelId);

            $this->clearAuth();

            $this->authRequest->createClient('targetHost');
            $this->amoAuth('targetLogin', 'targetHash');
            $this->funnelRequest->createClient('targetHost');
            $funnelId = $this->getFunnelIdByFunnelName('Воронка 1.1');

            $this->dealRequest->createClient('targetHost');
            $funnel1Deals = $this->getDealsByFunnelId($funnelId);

            $dealsToFunnel1 = DealManager::getDealsToTarget($icTurboDeals, $funnel1Deals);

            dump($dealsToFunnel1);
            exit;

            $this->logger->info('Создание отчета успешно завершено. Всего файлов: %s');
        } catch (MissingParams $exc) {
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
        $objectResponse = new \AmoCrm\Response\AuthResponse(
            \GuzzleHttp\json_decode(
                $this->authRequest
                    ->auth($login, $hash)
                    ->getBody()
                    ->getContents(),
                true,
                JSON_UNESCAPED_UNICODE
            )
        );

        if ($objectResponse->getError()) {
            throw new AuthError($objectResponse->getError());
        }

        $this->funnelRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->dealRequest->setCookie(
            $this->authRequest->getCookie()
        );
    }

    /**
     * @param array $targetDeals
     * @return array
     */
    private function addNewTargetDeal(array $targetDeals = []): array
    {
        return $this->dealRequest->addDeal($targetDeals)->getBody()->getContents();
    }

    /**
     * @param null|int $funnelId
     *
     * @return array
     */
    private function getDealsByFunnelId(int $funnelId = null): array
    {
        return $this->dealRequest->getDealsByFunnelId($funnelId);
    }

    /**
     * @param null|string $funnelName
     *
     * @return null|int
     */
    private function getFunnelIdByFunnelName(string $funnelName = null): ?int
    {
        return $this->funnelRequest->getFunnelIdByFunnelName($this->funnelRequest->getFunnel(), $funnelName);
    }

    private function clearAuth(): void
    {
        $this->authRequest->clearAuth();
        $this->dealRequest->clearAuth();
        $this->funnelRequest->clearAuth();
    }
}
