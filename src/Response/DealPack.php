<?php

namespace AmoCrm\Response;

/**
 * Description of DealPack.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class DealPack
{
    /**
     * @var null|array
     */
    private $items;

    /**
     * LeadInfo constructor.
     *
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        $this->items = $mapData;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
