<?php

namespace AmoCrm\Response;

/**
 * Description of FunnelResponse.
 *
 * @author Vitaly Dergunov <v.dergunov@icontext.ru>
 */
class FunnelResponse
{
    /**
     * @var null|int
     */
    private $bannerId;

    /**
     * @var null|int
     */
    private $campaignId;

    /**
     * @var null|\DateTime
     */
    private $createdTime;

    /**
     * @var null|int
     */
    private $formId;

    /**
     * @var null|string
     */
    private $formName;

    /**
     * @var null|int
     */
    private $id;

    /**
     * @var null|string
     */
    private $userBirthday;

    /**
     * @var null|string
     */
    private $userEmail;

    /**
     * @var null|string
     */
    private $userFullname;

    /**
     * @var null|string
     */
    private $userGeo;

    /**
     * @var null|string
     */
    private $userPhone;

    /**
     * @var null|string
     */
    private $userQuestions;

    /**
     * @var null|string
     */
    private $userSex;

    /**
     * LeadInfo constructor.
     *
     * @param array $mapData
     */
    public function __construct(array $mapData = [])
    {
        foreach ($mapData as $key => $val) {
            $key = lcfirst(str_replace('_', '', ucwords($key, '_')));
            if (property_exists(__CLASS__, $key)) {
                $this->{$key} = (is_array($val) && count($val) == 0)? null : $val;
            }
        }
    }

    /**
     * @return string
     */
    public function getUserQuestions(): ?string
    {
        return $this->userQuestions;
    }

    /**
     * @param null|string $userQuestion
     *
     * @return LeadInfo
     */
    public function setUserQuestion(string $userQuestion = null): self
    {
        $this->userQuestions = $userQuestion;

        return $this;
    }

    /**
     * @return int
     */
    public function getBannerId(): ?int
    {
        return $this->bannerId;
    }

    /**
     * @param null|int $bannerId
     *
     * @return LeadInfo
     */
    public function setBannerId(int $bannerId = null): self
    {
        $this->bannerId = $bannerId;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getCampaignId(): ?int
    {
        return $this->campaignId;
    }

    /**
     * @param null|int $campaignId
     *
     * @return LeadInfo
     */
    public function setCampaignId(int $campaignId = null): self
    {
        $this->campaignId = $campaignId;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreatedTime(): ?string
    {
        return $this->createdTime;
    }

    /**
     * @param null|string $createdTime
     *
     * @return LeadInfo
     */
    public function setCreatedTime(string $createdTime = null): self
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getFormId(): ?int
    {
        return $this->formId;
    }

    /**
     * @param null|int $formId
     *
     * @return LeadInfo
     */
    public function setFormId(int $formId = null): self
    {
        $this->formId = $formId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFormName(): ?string
    {
        return $this->formName;
    }

    /**
     * @param null|string $formName
     *
     * @return LeadInfo
     */
    public function setFormName(string $formName = null): self
    {
        $this->formName = $formName;

        return $this;
    }

    /**
     * @return null|int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param null|int $id
     *
     * @return LeadInfo
     */
    public function setId(int $id = null): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserBirthday(): ?string
    {
        return $this->userBirthday;
    }

    /**
     * @param null|string $userBirthday
     *
     * @return LeadInfo
     */
    public function setUserBirthday(string $userBirthday = null): self
    {
        $this->userBirthday = $userBirthday;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    /**
     * @param null|string $userEmail
     *
     * @return LeadInfo
     */
    public function setUserEmail(string $userEmail = null): self
    {
        $this->userEmail = $userEmail;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserFullname(): ?string
    {
        return $this->userFullname;
    }

    /**
     * @param null|string $userFullname
     *
     * @return LeadInfo
     */
    public function setUserFullname(string $userFullname = null): self
    {
        $this->userFullname = $userFullname;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserGeo(): ?string
    {
        return $this->userGeo;
    }

    /**
     * @param null|string $userGeo
     *
     * @return LeadInfo
     */
    public function setUserGeo(string $userGeo = null): self
    {
        $this->userGeo = $userGeo;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserPhone(): ?string
    {
        return $this->userPhone;
    }

    /**
     * @param null|string $userPhone
     *
     * @return LeadInfo
     */
    public function setUserPhone(string $userPhone = null): self
    {
        $this->userPhone = $userPhone;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUserSex(): ?string
    {
        return $this->userSex;
    }

    /**
     * @param null|string $userSex
     *
     * @return LeadInfo
     */
    public function setUserSex(string $userSex = null): self
    {
        $this->userSex = $userSex;

        return $this;
    }
}
