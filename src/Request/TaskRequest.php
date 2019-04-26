<?php

namespace AmoCrm\Request;

use AmoCrm\Response\TaskResponse;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class TaskRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class TaskRequest extends AbstractRequest
{
    /**
     * DealRequest constructor.
     *
     * @param ParameterBag $parameterBag
     */
    public function __construct(ParameterBag $parameterBag)
    {
        parent::__construct($parameterBag);
    }

    /**
     * @param array $deals
     *
     * @return array
     */
    public function getTasksOfDeals(array $deals = []): array
    {
        $this->setRequstUri(
            $this->parameterBag->get('taskGet')
        );

        $limit = 100;
        $offset = 0;
        $dealTasks = [];

        foreach ($deals as $deal) {
            $this->setQueryParams([
                'element_id' => $deal['id'],
                'type' => 'lead',
                'limit_rows' => $limit,
                'limit_offset' => ($offset * $limit),
            ]);
            $this->setHttpMethod('GET');

            $taskResult = $this->request()->getBody()->getContents();

            if (!$taskResult) {
                continue;
            }

            $dealTasks[$deal['id']] =
                new TaskResponse(
                    json_decode(
                        $taskResult,
                        true,
                        JSON_UNESCAPED_UNICODE
                    )
                );
        }

        return $dealTasks;
    }

    /**
     * @param array $params
     *
     * @return Response
     */
    public function addTask(array $params = []): Response
    {
        return $this->taskPostRequest($params);
    }

    /**
     * @param array $tasksToUpdate
     *
     * @return Response
     */
    public function updateDealsStatuses(array $tasksToUpdate = []): Response
    {
        return $this->taskPostRequest($tasksToUpdate);
    }

    /**
     * @return TaskRequest
     */
    public function clearAuth(): self
    {
        $this->clearCookie();

        return $this;
    }

    /**
     * @param array $params
     *
     * @return Response
     */
    private function taskPostRequest(array $params = []): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('taskAdd')
        );
        $this->setHttpMethod('POST');
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}
