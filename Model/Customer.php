<?php
declare(strict_types=1);
/**
 */

namespace CommerceLeague\ActiveCampaign\Model;

use CommerceLeague\ActiveCampaign\Api\Data\CustomerInterface;
use CommerceLeague\ActiveCampaign\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Customer
 */
class Customer extends AbstractModel implements CustomerInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(CustomerResource::class);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->_getData(self::ENTITY_ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->_getData(self::EMAIL);
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email): CustomerInterface
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritDoc
     */
    public function getActiveCampaignId()
    {
        return $this->_getData(self::ACTIVE_CAMPAIGN_ID);
    }

    /**
     * @inheritDoc
     */
    public function setActiveCampaignId($activeCampaignId): CustomerInterface
    {
        return $this->setData(self::ACTIVE_CAMPAIGN_ID, $activeCampaignId);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->_getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt): CustomerInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->_getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt): CustomerInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
