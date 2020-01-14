<?php
declare(strict_types=1);
/**
 * Copyright © André Flitsch. All rights reserved.
 * See license.md for license details.
 */

namespace CommerceLeague\ActiveCampaign\Observer\Customer;

use CommerceLeague\ActiveCampaign\Api\Data\ContactInterface;
use CommerceLeague\ActiveCampaign\Helper\Config as ConfigHelper;
use CommerceLeague\ActiveCampaign\MessageQueue\Topics;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\MessageQueue\PublisherInterface;

/**
 * Class AssignContactToListObserver
 *
 * @package CommerceLeague\ActiveCampaign\MessageQueue\Customer
 */
class AssignContactToListObserver implements ObserverInterface
{

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @param ConfigHelper       $configHelper
     * @param PublisherInterface $publisher
     */
    public function __construct(
        ConfigHelper $configHelper,
        PublisherInterface $publisher
    ) {
        $this->configHelper = $configHelper;
        $this->publisher    = $publisher;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->configHelper->isEnabled() || !$this->configHelper->isContactExportEnabled()) {
            return;
        }

        /** @var ContactInterface $contact */
        $contact = $observer->getEvent()->getData('contact');

        $this->publisher->publish(
            Topics::ASSIGN_CONTACT_TO_LIST,
            json_encode(
                [
                    'contact_id' => $contact->getId(),
                    'list_id'    => $this->configHelper->getRegisteredCustomerListId()
                ]
            )
        );
    }
}