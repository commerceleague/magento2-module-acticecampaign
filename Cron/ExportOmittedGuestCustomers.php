<?php
declare(strict_types=1);
/**
 */

namespace CommerceLeague\ActiveCampaign\Cron;

use CommerceLeague\ActiveCampaign\Api\Data\GuestCustomerInterface;
use CommerceLeague\ActiveCampaign\Helper\Config as ConfigHelper;
use CommerceLeague\ActiveCampaign\MessageQueue\Topics;
use CommerceLeague\ActiveCampaign\Model\ResourceModel\ActiveCampaign\GuestCustomer\Collection as CustomerCollection;
use CommerceLeague\ActiveCampaign\Model\ResourceModel\ActiveCampaign\GuestCustomer\CollectionFactory as CustomerCollectionFactory;
use Magento\Framework\MessageQueue\PublisherInterface;

/**
 * Class ExportOmittedCustomers
 */
class ExportOmittedGuestCustomers implements CronInterface
{

    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @param ConfigHelper              $configHelper
     * @param CustomerCollectionFactory $customerCollectionFactory
     * @param PublisherInterface        $publisher
     */
    public function __construct(
        ConfigHelper $configHelper,
        CustomerCollectionFactory $customerCollectionFactory,
        PublisherInterface $publisher
    ) {
        $this->configHelper              = $configHelper;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->publisher                 = $publisher;
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        if (!$this->configHelper->isEnabled() || !$this->configHelper->isCustomerExportEnabled()) {
            return;
        }

        $guestCustomers = $this->getCustomers();

        /** @var GuestCustomerInterface $customer */
        foreach ($guestCustomers as $customer) {
            $this->publisher->publish(
                Topics::GUEST_CUSTOMER_EXPORT,
                json_encode(
                    [
                        'magento_customer_id' => null,
                        'customer_is_guest'   => true,
                        'customer_data'       => [
                            GuestCustomerInterface::FIRSTNAME => $customer->getFirstname(),
                            GuestCustomerInterface::LASTNAME  => $customer->getLastname(),
                            GuestCustomerInterface::EMAIL     => $customer->getEmail()
                        ]
                    ]
                )
            );
        }
    }

    /**
     * @return array
     */
    private function getCustomers(): array
    {
        /** @var CustomerCollection $customerCollection */
        $customerCollection = $this->customerCollectionFactory->create();
        $customerCollection->addOmittedFilter();

        return $customerCollection->getItems();
    }
}
