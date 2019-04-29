<?php

namespace AmoCrm\Request;

use AmoCrm\Response\NoteResponse;
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
     * @param array $entityData
     * @param array $params
     *
     * @return array
     */
    public function getNotes(array $entityData = [], array $params = []): array
    {
        $this->setRequstUri(
            $this->parameterBag->get('noteGet')
        );

        $limit = 100;
        $offset = 0;
        $notes = [];

        foreach ($entityData as $oldNodeId => $node) {
            foreach ($node->getItems() as $item) {
                $this->setQueryParams([
                    'type' => $params['type'], // contact/lead/company/task
                    'element_id' => $item['id'],
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
                    //'note_type' => $params['note_type'],
                    'limit_rows' => $limit,
                    'limit_offset' => ($offset * $limit),
                ]);
                $this->setHttpMethod('GET');

                $result = $this->request()->getBody()->getContents();

                if (!$result) {
                    continue;
                }

                $notes[$item['id']] =
                    new NoteResponse(
                        \GuzzleHttp\json_decode(
                            $result,
                            true,
                            JSON_UNESCAPED_UNICODE
                        )
                    );
                ++$offset;
            }
        }

        return $notes;
    }

    /**
     * @param array $entityData
     * @param array $params
     *
     * @return array
     */
    public function getNotesOfDeals(array $entityData = [], array $params = []): array
    {
        $params = array_merge([
           'type' => 'lead',
        ], $params);

        return $this->getNotes($entityData, $params);
    }

    /**
     * @param array $entityData
     * @param array $params
     *
     * @return array
     */
    public function getNotesOfContacts(array $entityData = [], array $params = []): array
    {
        $params = array_merge([
            'type' => 'contact',
        ], $params);

        return $this->getNotes($entityData, $params);
    }

    /**
     * @param array $entityData
     * @param array $params
     *
     * @return array
     */
    public function getNotesOfCompanies(array $entityData = [], array $params = []): array
    {
        $params = array_merge([
            'type' => 'company',
        ], $params);

        return $this->getNotes($entityData, $params);
    }

    /**
     * @param array $entityData
     * @param array $params
     *
     * @return array
     */
    public function getNotesOfTasks(array $entityData = [], array $params = []): array
    {
        $params = array_merge([
            'type' => 'task',
        ], $params);

        return $this->getNotes($entityData, $params);
    }

    /**
     * @param array $entityData
     * @param array $params
     *
     * @return array
     */
    public function getCommonNotes(array $entityData = [], array $params = []): array
    {
        $params = array_merge([
            'note_type' => 'COMMON',
        ], $params);

        return $this->getNotes($entityData, $params);
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
