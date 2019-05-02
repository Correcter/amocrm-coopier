<?php

namespace AmoCrm\Request;

use AmoCrm\Response\TaskResponse;
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

        $this->setRequstUri(
            $this->parameterBag->get('requestTask')
        );
    }

    /**
     * @param array $deals
     *
     * @return array
     */
    public function getTasksOfDeals(array $deals = []): array
    {
        $dealTasks = [];

        foreach ($deals as $deal) {
            $this->setQueryParams([
                'element_id' => $deal['id'],
                'type' => 'lead',
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
}
