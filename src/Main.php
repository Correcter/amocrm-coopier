<?php

namespace AmoCrm;

use AmoCrm\Exceptions\AuthError;
use AmoCrm\Manager\ServiceManager;

/**
 * Class MainManager.
 */
class Main
{
    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * MainManager constructor.
     *
     * @param ServiceManager $serviceManager
     */
    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return bool
     */
    public function copy(): bool
    {
        try {
            $this->serviceManager
                ->copyBasicDataInitialize()
                ->clearAuth()
                ->copyTargetDataInitialize();

            if ($this->serviceManager->ifNeedToAdd()) {
                $this->serviceManager->buildCustomFields();
                $this->serviceManager
                    ->customFieldsOfDealsRequest()
                    ->customFieldsOfContactsRequest()
                    ->customFieldsOfCompanyRequest();
                $this->serviceManager->updateCustomFields();

                $this->serviceManager
                    ->dealRequest()
                    ->setUpDependenciesOfDeals();

                $this->serviceManager->buildTasksToTarget();
                $this->serviceManager->tasksRequest();

                $this->serviceManager->buildContactsToTarget();
                $this->serviceManager->contactRequest();

                $this->serviceManager->buildCompaniesToTarget();
                $this->serviceManager->companyRequest();

                $this->serviceManager->buildNotesToTarget('add');
                $this->serviceManager->notesRequest();
            }

        } catch (AuthError $exc) {
            echo $exc->getMessage();
            exit;
        } catch (\InvalidArgumentException $exc) {
            echo $exc->getMessage();
            exit;
        } finally {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function updateStatus(): bool
    {
        try {
            $this->serviceManager->updateBasicDataInitialize();
            $this->serviceManager->clearAuth();
            $this->serviceManager->updateTargetDataInitialize();
            $this->serviceManager->buildBasicFromTargetStatuses();

            if ($this->serviceManager->hasDealsToUpdate()) {
                $this->serviceManager->dealRequest();

                return true;
            }

        } catch (AuthError $exc) {
            echo $exc->getMessage();
        } catch (\InvalidArgumentException $exc) {
            echo $exc->getMessage();
        } finally {
            return false;
        }
    }

    /**
     * @return null|string
     */
    public function getCopyResult(): ?string
    {
        return $this->serviceManager->buildCopyStat();
    }
}
