<?php

namespace AmoCrm\Command;

use AmoCrm\Exceptions\AuthError;
use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use AmoCrm\Response\DealResponse;
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
     * @var DealManager
     */
    private $dealManager;

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
     * DealBasicToTargetCommand constructor.
     * @param DealManager $dealManager
     * @param AuthRequest $authRequest
     * @param DealRequest $dealRequest
     * @param FunnelRequest $funnelRequest
     * @param LoggerInterface $logger
     */
    public function __construct(
        DealManager $dealManager,
        AuthRequest $authRequest,
        DealRequest $dealRequest,
        FunnelRequest $funnelRequest,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);

        $this->dealManagert = $dealManager;
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
            $funnelId = $this->getFunnelIdByFunnelName('Sociorama');

            $this->dealRequest->createClient('basicHost');
            $socioramaDeals = $this->getDealsByFunnelId($funnelId);

            $this->dealManager->writeDealsIfNotExists($socioramaDeals, $funnelId);

            dump($socioramaDeals);
            exit;

            $this->clearAuth();

            $this->authRequest->createClient('targetHost');
            $this->amoAuth('targetLogin', 'targetHash');
            $this->funnelRequest->createClient('targetHost');
            $funnelId = $this->getFunnelIdByFunnelName('Воронка');

            $this->dealRequest->createClient('targetHost');
            $funnel1Deals = $this->getDealsByFunnelId($funnelId);

            $dealsToFunnel1 =
                DealManager::getDealsToTarget(
                    [
                        'status_id' => 142,
                        'pipeline_id' => $funnelId,
                    ],
                    $icTurboDeals,
                    $funnel1Deals
                );

            if (!$dealsToFunnel1) {
                $mess = 'Количество сделок в Воронка 1.1 актуально. Действие не требуется.';
                $output->writeln($mess);
                $this->logger->info($mess);
                exit;
            }

            if (!$this->addNewTargetDeal($dealsToFunnel1)->getItems()) {
                throw new \RuntimeException('Во время добавления сделки произошла ошибка');
            }

            $mess = sprintf('Добавлено сделок: %1$s', count($dealsToFunnel1));
            $output->writeln($mess);
            $this->logger->info($mess);
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
     *
     * @return DealResponse
     */
    private function addNewTargetDeal(array $targetDeals = []): DealResponse
    {
        return
            new DealResponse(
                    \GuzzleHttp\json_decode(
                        $this
                            ->dealRequest
                            ->addDeal($targetDeals)
                            ->getBody()
                            ->getContents(),
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
            );
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
