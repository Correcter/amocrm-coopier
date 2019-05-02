<?php

namespace AmoCrm\Response;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of CompanyResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class CompanyResponse
{
    /**
     * @var null|ArrayCollection
     */
    private $items;

    /**
     * LeadInfo constructor.
     *
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        if (!isset($mapData['_embedded']['items'])) {
            throw new \RuntimeException('Невалидный ответ от сервера!');
        }
        $this->items = new ArrayCollection();
        foreach ($mapData['_embedded']['items'] as $key => $val) {
            if (!$this->items->contains($val)) {
                $this->items->set((int) $key, $val);
            }
        }
    }

    /**
     * @return ArrayCollection
     */
    public function getItems(): ArrayCollection
    {
        return $this->items;
    }

    /**
     * @param int|null $itemId
     * @param array $customFields
     */
    public function replaceCustomFields(int $itemId = null, array $customFields = [])
    {
        if($this->items->containsKey($itemId)) {
            $item = $this->items->get($itemId);
            $item['custom_fields'] = $customFields;
            $this->items->set($itemId, $item);
        }
    }
}
