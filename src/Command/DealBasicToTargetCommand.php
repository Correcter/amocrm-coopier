<?php

namespace AmoCrm\Command;

use AmoCrm\Exceptions\AuthError;
use AmoCrm\Exceptions\InvalidRequest;
use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\ContactRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use AmoCrm\Request\TaskRequest;
use AmoCrm\Response\DealResponse;
use AmoCrm\Response\TaskResponse;
use AmoCrm\Service\DealManager;
use AmoCrm\Service\TaskManager;
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
     * @var TaskRequest
     */
    private $taskRequest;

    /**
     * @var ContactRequest
     */
    private $contactRequest;

    /**
     * DealBasicToTargetCommand constructor.
     *
     * @param DealManager     $dealManager
     * @param AuthRequest     $authRequest
     * @param DealRequest     $dealRequest
     * @param FunnelRequest   $funnelRequest
     * @param TaskRequest     $taskRequest
     * @param ContactRequest  $contactRequest
     * @param LoggerInterface $logger
     */
    public function __construct(
        DealManager $dealManager,
        AuthRequest $authRequest,
        DealRequest $dealRequest,
        FunnelRequest $funnelRequest,
        TaskRequest $taskRequest,
        ContactRequest $contactRequest,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);

        $this->dealManager = $dealManager;
        $this->authRequest = $authRequest;
        $this->dealRequest = $dealRequest;
        $this->funnelRequest = $funnelRequest;
        $this->taskRequest = $taskRequest;
        $this->contactRequest = $contactRequest;
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

            $this->taskRequest->createClient('basicHost');
            $dealOldTasks = $this->getTasksOfDeals($socioramaDeals);

//            dump($socioramaDeals, $dealOldTasks);
//            exit;

//            $dealContacts = $this->getContactsOfDeals($socioramaDeals);

            $this->clearAuth();

//            if($this->dealManager->writeDealsIfNotExists($socioramaDeals, $funnelId)) {
//
//            }

            $this->authRequest->createClient('targetHost');
            $this->amoAuth('targetLogin', 'targetHash');
            $this->funnelRequest->createClient('targetHost');
            $funnelId = $this->getFunnelIdByFunnelName('Воронка');

            $this->dealRequest->createClient('targetHost');
            $targetFunnelDeals = $this->getDealsByFunnelId($funnelId);

            $dealsToTargetFunnel =
                DealManager::getDealsToTarget(
                    [
                        'pipeline_id' => $funnelId,
                    ],
                    $socioramaDeals,
                    $targetFunnelDeals
                );

            if (!$dealsToTargetFunnel) {
                $mess = 'Количество сделок в "Воронка" актуально. Действие не требуется.';
                $output->writeln("<info>${mess}</info>");
                $this->logger->info($mess);
                exit;
            }

            $newDeals = $this->addNewTargetDeal($dealsToTargetFunnel)->getItems();

            $tasksToTarget =
                TaskManager::buildTasksToTarget(
                    $newDeals,
                    $dealOldTasks
                );

            $this->addNewTask($tasksToTarget);

            if (!$newDeals) {
                throw new \RuntimeException('Во время добавления сделки произошла ошибка');
            }

            $mess = sprintf('Добавлено сделок: %1$s', count($dealsToTargetFunnel));
            $output->writeln($mess);
            $this->logger->info($mess);
        } catch (\RuntimeException $exc) {
            echo $exc->getMessage();
        } catch (InvalidRequest $exc) {
            $output->writeln($exc->getMessage());
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

        $this->taskRequest->setCookie(
            $this->authRequest->getCookie()
        );
    }

    /**
     * @param array $dealsData
     *
     * @return TaskResponse
     */
    private function addNewTask(array $dealsData = [])
    {
        return
            new TaskResponse(
                \GuzzleHttp\json_decode(
                    $this
                        ->taskRequest
                        ->addTask($dealsData)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                )
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
     * @param array $deals
     *
     * @return array
     */
    private function getContactsOfDeals(array $deals = []): array
    {
        return $this->contactRequest->getContactsOfDeals($deals);
    }

    /**
     * @param array $deals
     *
     * @return array
     */
    private function getTasksOfDeals(array $deals = []): array
    {
        return $this->taskRequest->getTasksOfDeals($deals);
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
