<?php
declare(strict_types=1);
/**
 */

namespace CommerceLeague\ActiveCampaign\MessageQueue\Sales;

use CommerceLeague\ActiveCampaign\Api\Data\OrderInterface;
use CommerceLeague\ActiveCampaign\Api\OrderRepositoryInterface;
use CommerceLeague\ActiveCampaign\Gateway\Client;
use CommerceLeague\ActiveCampaign\Gateway\Request\OrderBuilder as OrderRequestBuilder;
use CommerceLeague\ActiveCampaign\Logger\Logger;
use CommerceLeague\ActiveCampaign\MessageQueue\AbstractConsumer;
use CommerceLeague\ActiveCampaign\MessageQueue\ConsumerInterface;
use CommerceLeague\ActiveCampaignApi\Exception\HttpException;
use CommerceLeague\ActiveCampaignApi\Exception\UnprocessableEntityHttpException;
use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface as MagentoOrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface as MagentoOrderRepositoryInterface;
use Magento\Sales\Model\Order as MagentoOrder;

/**
 * Class ExportOrderConsumer
 */
class ExportOrderConsumer extends AbstractConsumer implements ConsumerInterface
{

    public function __construct(
        private readonly MagentoOrderRepositoryInterface $magentoOrderRepository,
        Logger $logger,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderRequestBuilder $orderRequestBuilder,
        private readonly Client $client
    ) {
        parent::__construct($logger);
    }

    /**
     *
     * @throws CouldNotSaveException
     * @throws Exception
     */
    public function consume(string $message): void
    {
        $message = json_decode($message, true, 512, JSON_THROW_ON_ERROR);

        try {
            /** @var MagentoOrderInterface|MagentoOrder $magentoOrder */
            $magentoOrder = $this->magentoOrderRepository->get($message['magento_order_id']);
        } catch (NoSuchEntityException $e) {
            $this->logException($e);
            return;
        }

        $order   = $this->orderRepository->getOrCreateByMagentoQuoteId($magentoOrder->getQuoteId());
        $request = $this->orderRequestBuilder->build($magentoOrder);

        try {
            $apiResponse = $this->performApiRequest($order, $request);
        } catch (UnprocessableEntityHttpException $e) {
            try {
                $apiResponse = $this->handleUnprocessableEntityHttpException($e, $request, self::RESPONSE_KEY_ORDER);
            } catch (UnprocessableEntityHttpException $e) {
                $this->logUnprocessableEntityHttpException($e, $request);
                return;
            }
        } catch (HttpException $e) {
            $this->logException($e);
            return;
        }

        $order->setActiveCampaignId($apiResponse['ecomOrder']['id']);

        $this->orderRepository->save($order);
    }

    /**
     * @inheritDoc
     */
    function processDuplicateEntity(array $request, string $key): array
    {
        $response = $this->client->getOrderApi()->listPerPage(
            1,
            0,
            [
                'filters' => [
                    'externalid' => $request['externalid']
                ]
            ]
        );
        return [$key => $response->getItems()[0]];
    }

    private function performApiRequest(OrderInterface $order, array $request): array
    {
        if ($activeCampaignId = $order->getActiveCampaignId()) {
            return $this->client->getOrderApi()->update((int)$activeCampaignId, ['ecomOrder' => $request]);
        } else {
            return $this->client->getOrderApi()->create(['ecomOrder' => $request]);
        }
    }
}
