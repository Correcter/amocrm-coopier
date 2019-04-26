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
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getNotes(array $params = []): array
    {
        $this->setRequstUri(
            $this->parameterBag->get('noteGet')
        );

        $limit = 100;
        $offset = 0;
        $dealTasks = [];

        $this->setQueryParams([
            'element_id' => $params['element_id'],
            'element_type' => $params['element_type'], // lead/contact/company/customer
            /*
             *
             * 1 DEAL_CREATED,
             * 2 CONTACT_CREATED,
             * 3 DEAL_STATUS_CHANGED,
             * 4 COMMON,
             * 12 COMPANY_CREATED,
             * 13 TASK_RESULT,
             * 25 SYSTEM,
             * 102 SMS_IN,
             * 103 SMS_OUT
             * */
            'note_type' => '',
            'limit_rows' => $limit,
            'limit_offset' => ($offset * $limit),
        ]);
        $this->setHttpMethod('GET');

        $notes[$deal['id']] =
            new TaskResponse(
                \GuzzleHttp\json_decode(
                    $this->request()->getBody()->getContents(),
                    true,
                    JSON_UNESCAPED_UNICODE
                )
            );

        return $dealTasks;
    }

    /**
     * @param array $contacts
     *
     * @return array
     */
    public function getNotesOfContacts(array $contacts = []): array
    {
        return $this->noteRequest->getNotesOfContacts($contacts);
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
     * @return NoteRequest
     */
    public function clearAuth(): self
    {
        $this->clearCookie();

        return $this;
    }

    /**
     * @param array $tasks
     *
     * @return array
     */
    protected function getNotesOfTasks(array $tasks = []): array
    {
        return $this->noteRequest->getNotesOfContacts($tasks);
    }

    /**
     * @param array $companies
     *
     * @return array
     */
    protected function getNotesOfCompanies(array $companies = []): array
    {
        return $this->noteRequest->getNotesOfCompanies($companies);
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
