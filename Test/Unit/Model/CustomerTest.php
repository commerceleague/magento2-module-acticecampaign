<?php
/**
 */

namespace CommerceLeague\ActiveCampaign\Test\Unit\Model;

use CommerceLeague\ActiveCampaign\Api\Data\CustomerInterface;
use CommerceLeague\ActiveCampaign\Model\Customer;
use CommerceLeague\ActiveCampaign\Model\ResourceModel\Customer as CustomerResource;
use Magento\Framework\Model\Context;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class CustomerTest extends TestCase
{
    /**
     * @var MockObject|Context
     */
    protected $context;

    /**
     * @var MockObject|CustomerResource
     */
    protected $resource;

    /**
     * @var Customer
     */
    protected $customer;

    protected function setUp()
    {
        $this->context = $this->createMock(Context::class);
        $this->resource = $this->createMock(CustomerResource::class);

        $this->customer = (new ObjectManager($this))->getObject(
            Customer::class,
            [
                'context' => $this->context,
                'resource' => $this->resource
            ]
        );
    }

    public function testGetId()
    {
        $entityId = 123;
        $this->customer->setData(CustomerInterface::ENTITY_ID, $entityId);
        $this->assertEquals($entityId, $this->customer->getId());
    }

    public function testSetId()
    {
        $entityId = 123;
        $this->customer->setId($entityId);
        $this->assertEquals($entityId, $this->customer->getData(CustomerInterface::ENTITY_ID));
    }

    public function testGetEmail()
    {
        $email = 'example@example.com';
        $this->customer->setData(CustomerInterface::EMAIL, $email);
        $this->assertEquals($email, $this->customer->getEmail());
    }

    public function testSetEmail()
    {
        $email = 'example@example.com';
        $this->customer->setEmail($email);
        $this->assertEquals($email, $this->customer->getData(CustomerInterface::EMAIL));
    }

    public function testGetActiveCampaignId()
    {
        $activeCampaignId = 123;
        $this->customer->setData(CustomerInterface::ACTIVE_CAMPAIGN_ID, $activeCampaignId);
        $this->assertEquals($activeCampaignId, $this->customer->getActiveCampaignId());
    }

    public function testSetActiveCampaignId()
    {
        $activeCampaignId = 123;
        $this->customer->setActiveCampaignId($activeCampaignId);
        $this->assertEquals($activeCampaignId, $this->customer->getData(CustomerInterface::ACTIVE_CAMPAIGN_ID));
    }

    public function testGetCreatedAt()
    {
        $createdAt = '2019-01-01 00:00:00';
        $this->customer->setData(CustomerInterface::CREATED_AT, $createdAt);
        $this->assertEquals($createdAt, $this->customer->getCreatedAt());
    }

    public function testSetCreatedAt()
    {
        $createdAt = '2019-01-01 00:00:00';
        $this->customer->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $this->customer->getData(CustomerInterface::CREATED_AT));
    }

    public function testGetUpdatedAt()
    {
        $updatedAt = '2019-01-01 00:00:00';
        $this->customer->setData(CustomerInterface::UPDATED_AT, $updatedAt);
        $this->assertEquals($updatedAt, $this->customer->getUpdatedAt());
    }

    public function testSetUpdatedAt()
    {
        $updatedAt = '2019-01-01 00:00:00';
        $this->customer->setUpdatedAt($updatedAt);
        $this->assertEquals($updatedAt, $this->customer->getData(CustomerInterface::UPDATED_AT));
    }
}
