<?php
/**
 */

namespace CommerceLeague\ActiveCampaign\Test\Unit\Observer\Customer;

use CommerceLeague\ActiveCampaign\Api\ContactRepositoryInterface;
use CommerceLeague\ActiveCampaign\Api\Data\ContactInterface;
use CommerceLeague\ActiveCampaign\Helper\Config as ConfigHelper;
use CommerceLeague\ActiveCampaign\Logger\Logger;
use CommerceLeague\ActiveCampaign\MessageQueue\Contact\CreateUpdateMessage;
use CommerceLeague\ActiveCampaign\MessageQueue\Contact\CreateUpdateMessageBuilder;
use CommerceLeague\ActiveCampaign\MessageQueue\Contact\CreateUpdatePublisher;
use CommerceLeague\ActiveCampaign\Observer\CustomerSaveAfterObserver;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Phrase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomerSaveAfterObserverTest extends TestCase
{
    /**
     * @var MockObject|ConfigHelper
     */
    protected $configHelper;

    /**
     * @var MockObject|ContactRepositoryInterface
     */
    protected $contactRepository;

    /**
     * @var MockObject|Logger
     */
    protected $logger;

    /**
     * @var MockObject|CreateUpdateMessageBuilder
     */
    protected $createUpdateMessageBuilder;

    /**
     * @var MockObject|CreateUpdatePublisher
     */
    protected $createUpdatePublisher;

    /**
     * @var MockObject|Observer
     */
    protected $observer;

    /**
     * @var MockObject|Event
     */
    protected $event;

    /**
     * @var MockObject|Customer
     */
    protected $customer;

    /**
     * @var MockObject|ContactInterface
     */
    protected $contact;

    /**
     * @var MockObject|CreateUpdateMessage
     */
    protected $createUpdateMessage;

    /**
     * @var CustomerSaveAfterObserver
     */
    protected $customerSaveAfterObserver;

    protected function setUp()
    {
        $this->configHelper = $this->createMock(ConfigHelper::class);
        $this->contactRepository = $this->createMock(ContactRepositoryInterface::class);
        $this->logger = $this->createMock(Logger::class);
        $this->createUpdateMessageBuilder = $this->createMock(CreateUpdateMessageBuilder::class);
        $this->createUpdatePublisher = $this->createMock(CreateUpdatePublisher::class);
        $this->observer = $this->createMock(Observer::class);
        $this->event = $this->createMock(Event::class);
        $this->customer = $this->createMock(Customer::class);
        $this->contact = $this->createMock(ContactInterface::class);
        $this->createUpdateMessage = $this->createMock(CreateUpdateMessage::class);

        $this->customerSaveAfterObserver = new CustomerSaveAfterObserver(
            $this->configHelper,
            $this->contactRepository,
            $this->logger,
            $this->createUpdateMessageBuilder,
            $this->createUpdatePublisher
        );
    }

    public function testExecuteApiNotEnabled()
    {
        $this->configHelper->expects($this->once())
            ->method('isApiEnabled')
            ->willReturn(false);

        $this->observer->expects($this->never())
            ->method('getEvent');

        $this->customerSaveAfterObserver->execute($this->observer);
    }

    public function testExecuteWithException()
    {
        $this->configHelper->expects($this->once())
            ->method('isApiEnabled')
            ->willReturn(true);

        $this->observer->expects($this->once())
            ->method('getEvent')
            ->willReturn($this->event);

        $this->event->expects($this->once())
            ->method('getData')
            ->with('customer')
            ->willReturn($this->customer);

        $exception = new CouldNotSaveException(new Phrase(''));

        $this->contactRepository->expects($this->once())
            ->method('getOrCreateByCustomer')
            ->with($this->customer)
            ->willThrowException($exception);

        $this->logger->expects($this->once())
            ->method('critical')
            ->with($exception);

        $this->createUpdatePublisher->expects($this->never())
            ->method('publish');

        $this->customerSaveAfterObserver->execute($this->observer);
    }

    public function testExecute()
    {
        $this->configHelper->expects($this->once())
            ->method('isApiEnabled')
            ->willReturn(true);

        $this->observer->expects($this->once())
            ->method('getEvent')
            ->willReturn($this->event);

        $this->event->expects($this->once())
            ->method('getData')
            ->with('customer')
            ->willReturn($this->customer);

        $this->contactRepository->expects($this->once())
            ->method('getOrCreateByCustomer')
            ->with($this->customer)
            ->willReturn($this->contact);

        $this->createUpdateMessageBuilder->expects($this->once())
            ->method('buildWithCustomer')
            ->with($this->contact, $this->customer)
            ->willReturn($this->createUpdateMessage);

        $this->createUpdatePublisher->expects($this->once())
            ->method('publish')
            ->with($this->createUpdateMessage);

        $this->customerSaveAfterObserver->execute($this->observer);
    }
}