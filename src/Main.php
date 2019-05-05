<?php

namespace AmoCrm;

use AmoCrm\Manager\ServiceManager;
use AmoCrm\Exceptions\AuthError;

/**
 * Class MainManager
 * @package AmoCrm\Manager
 */
class Main {

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * MainManager constructor.
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
            $this->serviceManager->copyBasicDataInitialize();
            $this->serviceManager->clearAuth();
            $this->serviceManager->copyTargetDataInitialize();

            if ($this->serviceManager->ifNeedToAdd()) {

//                $this->serviceManager->buildCustomFields();
//                $this->serviceManager->customFieldsOfDealsRequest();
//                $this->serviceManager->customFieldsOfContactsRequest();
//                $this->serviceManager->customFieldsOfCompanyRequest();
//                $this->serviceManager->updateCustomFields();

                $this->serviceManager->dealRequest();
                $this->serviceManager->setUpDependenciesOfDeals();
//                $this->serviceManager->buildTasksToTarget();
//                $this->serviceManager->tasksRequest();


                $this->serviceManager->buildCompaniesToTarget();
                $this->serviceManager->companyRequest();

                $this->serviceManager->buildContactsToTarget();
                $this->serviceManager->contactRequest();

                $this->serviceManager->buildCompaniesToTarget();
                $this->serviceManager->companyRequest();

                $this->serviceManager->buildContactsToTarget();
                $this->serviceManager->contactRequest();

                $this->serviceManager->buildNotesToTarget('add');
                $this->serviceManager->notesRequest();
                return true;
            }

            return false;
        } catch(AuthError $exc) {
            echo $exc->getMessage();
            exit;
        } catch(\InvalidArgumentException $exc) {
            echo $exc->getMessage();
            exit;
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

            if($this->serviceManager->hasDealsToUpdate()) {
                $this->serviceManager->dealRequest();
                return true;
            }
            return false;
        } catch(AuthError $exc) {
            echo $exc->getMessage();
        } catch(\InvalidArgumentException $exc) {
            echo $exc->getMessage();
        }
    }

    /**
     * @return null|string
     */
    public function getCopyResult(): ?string {
        return $this->serviceManager->buildCopyStat();
    }
}