<?php

namespace AmoCrm\Request;

use AmoCrm\Response\TaskResponse;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class NoteRequest.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class NoteRequest extends AbstractRequest
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
    public function getNotesOfDeals(array $deals = []): array
    {
        $this->setRequstUri(
            $this->parameterBag->get('noteGet')
        );

        $limit = 100;
        $offset = 0;
        $dealTasks = [];

        foreach ($deals as $deal) {
            $this->setQueryParams([
                'element_id' => $deal['id'],
                'note_type' => 'lead', // lead/contact/company/customer
                'limit_rows' => $limit,
                'limit_offset' => ($offset * $limit),
            ]);
            $this->setHttpMethod('GET');

            $dealTasks[$deal['id']] =
                new TaskResponse(
                    \GuzzleHttp\json_decode(
                        $this->request()->getBody()->getContents(),
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
    public function addNote(array $params = []): Response
    {
        return $this->notePostRequest($params);
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
    private function notePostRequest(array $params = []): Response
    {
        $this->setRequstUri(
            $this->parameterBag->get('noteAdd')
        );
        $this->setHttpMethod('POST');
        $this->addHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setBody(
            \GuzzleHttp\json_encode($params, JSON_UNESCAPED_UNICODE)
        );

        return $this->request();
    }
}
