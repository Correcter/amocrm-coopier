<?php

namespace AmoCrm\Response;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AbstractResponse
 * @package AmoCrm\Response
 */
class AbstractResponse
{
    /**
     * @var null|ArrayCollection
     */
    protected $items;

    /**
     * AbstractResponse constructor.
     * @param array $mapData
     */
    protected function __construct(array $mapData = [])
    {
        $this->items = new ArrayCollection();
        if (isset($mapData['_embedded']['items'])) {
            foreach ($mapData['_embedded']['items'] as $key => $val) {
                if (!$this->items->contains($val)) {
                    $this->items->set((int) $key, $val);
                }
            }
        }
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

    /**
     * @return ArrayCollection
     */
    public function getItems(): ArrayCollection
    {
        return $this->items;
    }

}