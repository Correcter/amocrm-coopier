<?php

namespace AmoCrm\Command;

use AmoCrm\Exceptions\AuthError;
use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\ContactRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use AmoCrm\Request\CustomerRequest;
use AmoCrm\Request\CompanyRequest;
use AmoCrm\Request\NoteRequest;
use AmoCrm\Request\TaskRequest;
use AmoCrm\Response\DealResponse;
use AmoCrm\Service\DealManager;
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
     * @var TaskRequest
     */
    private $taskRequest;

    /**
     * @var ContactRequest
     */
    private $contactRequest;

    /**
     * @var CompanyRequest
     */
    private $companyRequest;

    /**
     * @var NoteRequest
     */
    private $noteRequest;

    /**
     * UpdateBasicDealStatusFromTargetCommand constructor.
     * @param DealManager $dealManager
     * @param AuthRequest $authRequest
     * @param DealRequest $dealRequest
     * @param FunnelRequest $funnelRequest
     * @param TaskRequest $taskRequest
     * @param ContactRequest $contactRequest
     * @param CompanyRequest $companyRequest
     * @param NoteRequest $noteRequest
     * @param LoggerInterface $logger
     */
    public function __construct(
        DealManager $dealManager,
        AuthRequest $authRequest,
        DealRequest $dealRequest,
        FunnelRequest $funnelRequest,
        TaskRequest $taskRequest,
        ContactRequest $contactRequest,
        CompanyRequest $companyRequest,
        NoteRequest $noteRequest,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);

        $this->dealManager = $dealManager;
        $this->authRequest = $authRequest;
        $this->dealRequest = $dealRequest;
        $this->funnelRequest = $funnelRequest;
        $this->taskRequest = $taskRequest;
        $this->contactRequest = $contactRequest;

        $this->companyRequest = $companyRequest;
        $this->noteRequest = $noteRequest;
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
            $this->authRequest->createClient('targetHost');
            $this->amoAuth('targetLogin', 'targetHash');
            $this->funnelRequest->createClient('targetHost');
            $this->dealRequest->createClient('targetHost');
            $funnelId = $this->getFunnelIdByFunnelName('Sociorama');
            $targetRunningDealNames = $this->findRunningDeals($funnelId, 142);

            $this->clearAuth();

            $this->authRequest->createClient('basicHost');
            $this->amoAuth('basicLogin', 'basicHash');
            $this->funnelRequest->createClient('basicHost');
            $this->dealRequest->createClient('basicHost');
            $funnelId = $this->getFunnelIdByFunnelName('iCTurbo');
            $icTurboDeals = $this->getDealsByFunnelId($funnelId);

            $dealsToUpdate =
                DealManager::updateBasicFromTargetArray(
                    [
                        'status_id' => 142,
                    ],
                    $icTurboDeals,
                    $targetRunningDealNames
                );

            if (!$dealsToUpdate) {
                $mess = 'Отсутствуют сделки с завершенным статусом. Действие не требуется';
                $output->writeln($mess);
                $this->logger->info($mess);
                exit;
            }

            $updatedDeals = $this->updateDealsStatuses($dealsToUpdate)->getItems();

            $mess = sprintf('Обновлено сделок: %1$s', count($updatedDeals));
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

    /**
     * @param array $dealsToUpdate
     *
     * @return DealResponse
     */
    private function updateDealsStatuses(array $dealsToUpdate = []): DealResponse
    {
        return
            new DealResponse(
                \GuzzleHttp\json_decode(
                    $this
                        ->dealRequest
                        ->updateDealsStatuses($dealsToUpdate)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                )
            );
    }

    /**
     * @param null|int $funnelId
     * @param null|int $statusId
     *
     * @return null|int
     */
    private function findRunningDeals(int $funnelId = null, int $statusId = null): array
    {
        return $this->dealRequest->getDealsByFunnelId($funnelId, $statusId);
    }

    private function clearAuth(): void
    {
        $this->authRequest->clearAuth();
        $this->dealRequest->clearAuth();
        $this->funnelRequest->clearAuth();
    }
}
