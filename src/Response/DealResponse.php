<?php

namespace AmoCrm\Response;

/**
 * Description of LeadForm.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class DealResponse
{
    /**
     * @var int[]
     */
    private $bannerIds;

    /**
     * @var int[]
     */
    private $campaignIds;

    /**
     * @var int
     */
    private $formId;

    /**
     * LeadForm constructor.
     *
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        $this->bannerIds = [];
        $this->campaignIds = [];

        foreach ($mapData as $key => $val) {
            $key = lcfirst(str_replace('_', '', ucwords($key, '_')));
            if (property_exists(__CLASS__, $key)) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * @return int[]
     */
    public function getBannerIds(): array
    {
        return $this->bannerIds;
    }

    /**
     * @param null|int $bannerId
     *
     * @return LeadForm
     */
    public function addBannerId(int $bannerId = null): self
    {
        if (!isset($this->bannerIds[$bannerId])) {
            $this->bannerIds[$bannerId] = $bannerId;
        }

        return $this;
    }

    /**
     * @return int[]
     */
    public function getCampaignIds(): array
    {
        return $this->campaignIds;
    }

    /**
     * @param null|int $campaignId
     *
     * @return LeadForm
     */
    public function addCampaignId(int $campaignId = null): self
    {
        if (!isset($this->campaignIds[$campaignId])) {
            $this->campaignIds[$campaignId] = $campaignId;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getFormId(): int
    {
        return $this->formId;
    }

    /**
     * @param int $formId
     *
     * @return LeadForm
     */
    public function setFormId(int $formId): self
    {
        $this->formId = $formId;

        return $this;
    }
}
