<?php

namespace AmoCrm\Command;

use AmoCrm\Exceptions\AuthError;
use AmoCrm\Exceptions\InvalidRequest;
use AmoCrm\Request\AuthRequest;
use AmoCrm\Request\CompanyRequest;
use AmoCrm\Request\ContactRequest;
use AmoCrm\Request\DealRequest;
use AmoCrm\Request\FunnelRequest;
use AmoCrm\Request\NoteRequest;
use AmoCrm\Request\TaskRequest;
use AmoCrm\Response\TaskResponse;
use AmoCrm\Service\CompanyManager;
use AmoCrm\Service\ContactManager;
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
     * @var CompanyRequest
     */
    private $companyRequest;

    /**
     * @var NoteRequest
     */
    private $noteRequest;

    /**
     * DealBasicToTargetCommand constructor.
     *
     * @param DealManager     $dealManager
     * @param AuthRequest     $authRequest
     * @param DealRequest     $dealRequest
     * @param FunnelRequest   $funnelRequest
     * @param TaskRequest     $taskRequest
     * @param ContactRequest  $contactRequest
     * @param CompanyRequest  $companyRequest
     * @param NoteRequest     $noteRequest
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
            $this->companyRequest->createClient('basicHost');
            $this->contactRequest->createClient('basicHost');

            //$dealOldTasks = $this->getTasksOfDeals($socioramaDeals);
            //$oldCompanies = $this->getCompaniesOfDeals($socioramaDeals);
            $oldContacts = $this->getContactsOfDeals($socioramaDeals);

//            dump($socioramaDeals, $oldCompanies, $oldContacts);
//            exit;

            $this->clearAuth();

//            if($this->dealManager->writeDealsIfNotExists($socioramaDeals, $funnelId)) {
//
//            }

            $this->authRequest->createClient('targetHost');
            $this->amoAuth('targetLogin', 'targetHash');
            $this->funnelRequest->createClient('targetHost');
            $this->taskRequest->createClient('targetHost');
            $this->dealRequest->createClient('targetHost');
            $this->companyRequest->createClient('targetHost');
            $this->contactRequest->createClient('targetHost');

            $funnelId = $this->getFunnelIdByFunnelName('Воронка');

            $targetFunnelDeals = $this->getDealsByFunnelId($funnelId);

            // Добавление сделок
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

            $resultDeals = $this->addNewTargetDeal($dealsToTargetFunnel);

            // Добавление задач
//            $tasksToTarget =
//                TaskManager::buildTasksToTarget(
//                    $resultDeals,
//                    $dealOldTasks
//                );
//
//            $resultTasks = $this->addNewTasks($tasksToTarget);

            // Добавление компаний
//            $companiesToTarget =
//                CompanyManager::buildCompaniesToTarget(
//                    $resultDeals,
//                    $oldCompanies
//                );
//
//            $resultCompanies = $this->addNewCompanies($companiesToTarget);

            // Добавление контактов
            $contactsToTarget =
                ContactManager::buildContactsToTarget(
                    $resultDeals,
                    $oldContacts
                );

            dump($resultDeals, $oldContacts, $contactsToTarget);
            exit;

            $resultContacts = $this->addNewContacts($contactsToTarget);

//            dump(
//                $resultDeals,
//                $dealOldTasks,
//                $tasksToTarget,
//                $resultTasks
//            );
//            exit;

            if (!$resultDeals) {
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

        $this->contactRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->companyRequest->setCookie(
            $this->authRequest->getCookie()
        );

        $this->noteRequest->setCookie(
            $this->authRequest->getCookie()
        );
    }

    /**
     * @param array $tasksData
     *
     * @return array
     */
    private function addNewTasks(array $tasksData = []): array
    {
        $newTasks = [];
        foreach ($tasksData as $task) {
            $newTasks[] = new TaskResponse(
                \GuzzleHttp\json_decode(
                    $this
                        ->taskRequest
                        ->addTask($task)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                )
            );
        }

        return $newTasks;
    }

    /**
     * @param array $companies
     *
     * @return array
     */
    private function addNewCompanies(array $companies = []): array
    {
        $newCompanies = [];
        foreach ($companies as $comp) {
            $newTasks[] = new TaskResponse(
                \GuzzleHttp\json_decode(
                    $this
                        ->companyRequest
                        ->addCompany($comp)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                )
            );
        }

        return $newCompanies;
    }

    /**
     * @param array $contacts
     *
     * @return array
     */
    private function addNewContacts(array $contacts = []): array
    {
        $newContacts = [];
        foreach ($contacts as $contact) {
            $newTasks[] = new TaskResponse(
                \GuzzleHttp\json_decode(
                    $this
                        ->contactRequest
                        ->addContact($contact)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                )
            );
        }

        return $newContacts;
    }

    /**
     * @param array $targetDeals
     *
     * @return array
     */
    private function addNewTargetDeal(array $targetDeals = []): array
    {
        $dealsResult = [];
        foreach ($targetDeals as $oldDealId => $deal) {
            $dealsResult[$oldDealId] =
                \GuzzleHttp\json_decode(
                    $this
                        ->dealRequest
                        ->addDeal($deal)
                        ->getBody()
                        ->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                );
        }

        return $dealsResult;
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
    private function getCompaniesOfDeals(array $deals = []): array
    {
        return $this->companyRequest->getCompaniesOfDeals($deals);
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
        $this->taskRequest->clearAuth();
        $this->contactRequest->clearAuth();
        $this->companyRequest->clearAuth();
        $this->noteRequest->clearAuth();
    }
}
