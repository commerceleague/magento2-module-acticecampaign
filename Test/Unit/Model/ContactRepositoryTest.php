<?php
/**
 */

namespace CommerceLeague\ActiveCampaign\Test\Unit\Model;

use CommerceLeague\ActiveCampaign\Model\Contact;
use CommerceLeague\ActiveCampaign\Model\ContactRepository;
use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Newsletter\Model\Subscriber;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use CommerceLeague\ActiveCampaign\Model\ResourceModel\Contact as ContactResource;
use CommerceLeague\ActiveCampaign\Model\ContactFactory;

class ContactRepositoryTest extends TestCase
{
    /**
     * @var MockObject|ContactResource
     */
    protected $contactResource;

    /**
     * @var MockObject|Contact
     */
    protected $contact;

    /**
     * @var MockObject|ContactFactory
     */
    protected $contactFactory;

    /**
     * @var MockObject|Customer
     */
    protected $customer;

    /**
     * @var MockObject|Subscriber
     */
    protected $subscriber;

    /**
     * @var ContactRepository
     */
    protected $contactRepository;

    protected function setUp()
    {
        $this->contactResource = $this->getMockBuilder(ContactResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contactFactory = $this->getMockBuilder(ContactFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->contact = $this->getMockBuilder(Contact::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contactFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->contact);

        $this->customer = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->subscriber = $this->getMockBuilder(Subscriber::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contactRepository = new ContactRepository(
            $this->contactResource,
            $this->contactFactory
        );
    }

    public function testSaveThrowsException()
    {
        $this->contactResource->expects($this->once())
            ->method('save')
            ->with($this->contact)
            ->willThrowException(new \Exception('an exception message'));

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('an exception message');

        $this->contactRepository->save($this->contact);
    }

    public function testSave()
    {
        $this->contactResource->expects($this->once())
            ->method('save')
            ->with($this->contact)
            ->willReturnSelf();

        $this->assertEquals($this->contact, $this->contactRepository->save($this->contact));
    }

    public function testGetById()
    {
        $contactId = 123;
        $this->assertEquals($this->contact, $this->contactRepository->getById($contactId));
    }

    public function testGetOrCreateByCustomerCreatesContact()
    {
        $email = 'email@example.com';

        $this->customer->expects($this->any())
            ->method('getData')
            ->with('email')
            ->willReturn($email);

        $this->contactResource->expects($this->once())
            ->method('load')
            ->with($this->contact, $email, 'email')
            ->willReturn($this->contact);

        $this->contact->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->contact->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();

        $this->contactResource->expects($this->once())
            ->method('save')
            ->with($this->contact)
            ->willReturnSelf();

        $this->assertSame($this->contact, $this->contactRepository->getOrCreateByCustomer($this->customer));
    }

    public function testGetOrCreateByCustomerLoadsContact()
    {
        $contactId = 678;
        $email = 'email@example.com';

        $this->customer->expects($this->any())
            ->method('getData')
            ->with('email')
            ->willReturn($email);

        $this->contactResource->expects($this->once())
            ->method('load')
            ->with($this->contact, $email, 'email')
            ->willReturn($this->contact);

        $this->contact->expects($this->once())
            ->method('getId')
            ->willReturn($contactId);

        $this->contact->expects($this->never())
            ->method('setEmail');

        $this->contactResource->expects($this->never())
            ->method('save');

        $this->assertSame($this->contact, $this->contactRepository->getOrCreateByCustomer($this->customer));
    }

    public function testGetOrCreateBySubscriberCreatesContact()
    {
        $email = 'email@example.com';

        $this->subscriber->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);

        $this->contactResource->expects($this->once())
            ->method('load')
            ->with($this->contact, $email, 'email')
            ->willReturn($this->contact);

        $this->contact->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->contact->expects($this->once())
            ->method('setEmail')
            ->with($email)
            ->willReturnSelf();

        $this->contactResource->expects($this->once())
            ->method('save')
            ->with($this->contact)
            ->willReturnSelf();

        $this->assertSame($this->contact, $this->contactRepository->getOrCreateBySubscriber($this->subscriber));
    }


    public function testGetOrCreateBySubscriberLoadsContact()
    {
        $contactId = 678;
        $email = 'email@example.com';

        $this->subscriber->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);

        $this->contactResource->expects($this->once())
            ->method('load')
            ->with($this->contact, $email, 'email')
            ->willReturn($this->contact);

        $this->contact->expects($this->once())
            ->method('getId')
            ->willReturn($contactId);

        $this->contact->expects($this->never())
            ->method('setEmail');

        $this->contactResource->expects($this->never())
            ->method('save');

        $this->assertSame($this->contact, $this->contactRepository->getOrCreateBySubscriber($this->subscriber));
    }


    public function testDeleteThrowsException()
    {
        $this->contactResource->expects($this->once())
            ->method('delete')
            ->with($this->contact)
            ->willThrowException(new \Exception('an exception message'));

        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('an exception message');

        $this->contactRepository->delete($this->contact);
    }

    public function testDelete()
    {
        $this->contactResource->expects($this->once())
            ->method('delete')
            ->with($this->contact)
            ->willReturnSelf();

        $this->assertTrue($this->contactRepository->delete($this->contact));
    }

    public function testDeleteByIdThrowsException()
    {
        $contactId = 123;

        $this->contact->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->contactResource->expects($this->once())
            ->method('load')
            ->with($this->contact, $contactId)
            ->willReturn($this->contact);

        $this->contactResource->expects($this->never())
            ->method('delete');

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('The Contact with the "123" ID doesn\'t exist');

        $this->contactRepository->deleteById($contactId);
    }

    public function testDeleteById()
    {
        $contactId = 123;

        $this->contact->expects($this->once())
            ->method('getId')
            ->willReturn($contactId);

        $this->contactResource->expects($this->once())
            ->method('load')
            ->with($this->contact, $contactId)
            ->willReturn($this->contact);

        $this->contactResource->expects($this->once())
            ->method('delete')
            ->with($this->contact)
            ->willReturnSelf();

        $this->assertTrue($this->contactRepository->deleteById($contactId));
    }
}
