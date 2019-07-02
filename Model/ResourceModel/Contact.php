<?php
declare(strict_types=1);
/**
 */
namespace CommerceLeague\ActiveCampaign\Model\ResourceModel;

use CommerceLeague\ActiveCampaign\Api\Data\ContactInterface;
use CommerceLeague\ActiveCampaign\Setup\SchemaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Contact
 */
class Contact extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(SchemaInterface::CONTACT_TABLE, ContactInterface::ENTITY_ID);
    }

    /**
     * @param array $contacts
     * @throws LocalizedException
     */
    public function importContacts(array $contacts): void
    {
        $this->getConnection()->insertOnDuplicate(
            $this->getMainTable(),
            $contacts,
            [ContactInterface::EMAIL, ContactInterface::ACTIVE_CAMPAIGN_ID]
        );
    }
}
