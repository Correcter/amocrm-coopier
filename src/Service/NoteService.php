<?php

namespace AmoCrm\Service;

/**
 * Class NoteService.
 *
 * @author Vitaly Dergunov <correcter@inbox.ru>
 */
class NoteService
{
    /**
     * @param array $newEntities
     * @param array $oldNotes
     * @param string|null $operationType
     * @return array
     */
    public function buildNotesToTarget(array $newEntities = [], array $oldNotes = [], string $operationType = null): array
    {
        $toTargetNotes = [];
        foreach ($oldNotes as $oldEntityId => $notes) {

            if(!$operationType) {
                throw new \InvalidArgumentException('Тип операции для событий (add/update) не назначен');
            }

            if(!isset($newEntities[$oldEntityId])) {
                throw new \InvalidArgumentException('В сущности отсутствуют идентификаторы для связи');
            }

            if(is_object($newEntities[$oldEntityId])) {
                $newEntities[$oldEntityId] = $newEntities[$oldEntityId]->getItems();
            }

            foreach ($notes->getItems() as $note) {

                foreach($newEntities[$oldEntityId] as $entity) {
                    $toTargetNotes[$oldEntityId][$operationType][] = [
                        'element_id' => $entity['id'] ?? 0,
                        'element_type' => $note['element_type'],
                        'text' => $note['text'],
                        'note_type' => $note['note_type'],
                        'created_at' => time(),
                        'updated_at' => time(),
                        'responsible_user_id' => $note['responsible_user_id'],
                        'params' => $note['params'] ?? []
                    ];
                }
            }
        }

        return $toTargetNotes;
    }
}
