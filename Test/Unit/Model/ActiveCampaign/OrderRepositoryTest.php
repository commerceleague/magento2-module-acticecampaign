<?php
declare(strict_types=1);
/**
 */

namespace CommerceLeague\ActiveCampaign\Test\Unit\Model\ActiveCampaign;

use CommerceLeague\ActiveCampaign\Api\Data\OrderInterface;
use CommerceLeague\ActiveCampaign\Model\ActiveCampaign\Order;
use CommerceLeague\ActiveCampaign\Model\ActiveCampaign\OrderFactory;
use CommerceLeague\ActiveCampaign\Model\ActiveCampaign\OrderRepository;
use CommerceLeague\ActiveCampaign\Model\ResourceModel\ActiveCampaign\Order as OrderResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderRepositoryTest extends TestCase
{
    /**
     * @var MockObject|OrderResource
     */
    protected $orderResource;

    /**
     * @var MockObject|Order
     */
    protected $order;

    /**
     * @var MockObject|OrderFactory
     */
    protected $orderFactory;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    protected function setUp()
    {
        $this->orderResource = $this->getMockBuilder(OrderResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderFactory = $this->getMockBuilder(OrderFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $this->order = $this->getMockBuilder(Order::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderFactory->expects($this->any())
            ->method('create')
            ->willReturn($this->order);

        $this->orderRepository = new OrderRepository(
            $this->orderResource,
            $this->orderFactory
        );
    }

    public function testSaveThrowsException()
    {
        $this->orderResource->expects($this->once())
            ->method('save')
            ->with($this->order)
            ->willThrowException(new \Exception('an exception message'));

        $this->expectException(CouldNotSaveException::class);
        $this->expectExceptionMessage('an exception message');

        $this->orderRepository->save($this->order);
    }


    public function testSave()
    {
        $this->orderResource->expects($this->once())
            ->method('save')
            ->with($this->order)
            ->willReturnSelf();

        $this->assertSame($this->order, $this->orderRepository->save($this->order));
    }

    public function testGetById()
    {
        $entityId = 123;

        $this->orderResource->expects($this->once())
            ->method('load')
            ->with($this->order, $entityId)
            ->willReturn($this->order);

        $this->assertSame($this->order, $this->orderRepository->getById($entityId));
    }

    public function testGetByMagentoCustomerId()
    {
        $magentoOrderId = 123;

        $this->orderResource->expects($this->once())
            ->method('load')
            ->with($this->order, $magentoOrderId, OrderInterface::MAGENTO_ORDER_ID)
            ->willReturn($this->order);

        $this->assertSame($this->order, $this->orderRepository->getByMagentoOrderId($magentoOrderId));
    }


    public function testGetOrCreateByMagentoOrderIdWithKnownMagentoCustomer()
    {
        $magentoOrderId = 123;

        $this->orderResource->expects($this->once())
            ->method('load')
            ->with($this->order, $magentoOrderId, OrderInterface::MAGENTO_ORDER_ID)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('getId')
            ->willReturn(123);

        $this->order->expects($this->never())
            ->method('setMagentoOrderId');

        $this->assertSame($this->order, $this->orderRepository->getOrCreateByMagentoOrderId($magentoOrderId));
    }

    public function testGetOrCreateByMagentoOrderId()
    {
        $magentoOrderId = 123;

        $this->orderResource->expects($this->once())
            ->method('load')
            ->with($this->order, $magentoOrderId, OrderInterface::MAGENTO_ORDER_ID)
            ->willReturn($this->order);

        $this->order->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->order->expects($this->once())
            ->method('setMagentoOrderId')
            ->with($magentoOrderId)
            ->willReturnSelf();

        $this->orderResource->expects($this->once())
            ->method('save')
            ->with($this->order)
            ->willReturn($this->order);

        $this->assertSame($this->order, $this->orderRepository->getOrCreateByMagentoOrderId($magentoOrderId));
    }


    public function testDeleteThrowsException()
    {
        $this->orderResource->expects($this->once())
            ->method('delete')
            ->with($this->order)
            ->willThrowException(new \Exception('an exception message'));

        $this->expectException(CouldNotDeleteException::class);
        $this->expectExceptionMessage('an exception message');

        $this->orderRepository->delete($this->order);
    }

    public function testDelete()
    {
        $this->orderResource->expects($this->once())
            ->method('delete')
            ->with($this->order)
            ->willReturnSelf();

        $this->assertTrue($this->orderRepository->delete($this->order));
    }

    public function testDeleteByIdThrowsException()
    {
        $entityId = 123;

        $this->order->expects($this->once())
            ->method('getId')
            ->willReturn(null);

        $this->orderResource->expects($this->once())
            ->method('load')
            ->with($this->order, $entityId)
            ->willReturn($this->order);

        $this->orderResource->expects($this->never())
            ->method('delete');

        $this->expectException(NoSuchEntityException::class);
        $this->expectExceptionMessage('The Order with the "123" ID doesn\'t exist');

        $this->orderRepository->deleteById($entityId);
    }

    public function testDeleteById()
    {
        $entityId = 123;

        $this->order->expects($this->once())
            ->method('getId')
            ->willReturn($entityId);

        $this->orderResource->expects($this->once())
            ->method('load')
            ->with($this->order, $entityId)
            ->willReturn($this->order);

        $this->orderResource->expects($this->once())
            ->method('delete')
            ->with($this->order)
            ->willReturnSelf();

        $this->assertTrue($this->orderRepository->deleteById($entityId));
    }
}
