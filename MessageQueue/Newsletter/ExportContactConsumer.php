<?php
declare(strict_types=1);
/**
 */

namespace CommerceLeague\ActiveCampaign\MessageQueue\Newsletter;

use CommerceLeague\ActiveCampaign\Api\ContactRepositoryInterface;
use CommerceLeague\ActiveCampaign\Gateway\Client;
use CommerceLeague\ActiveCampaign\Gateway\Request\ContactBuilder as ContactRequestBuilder;
use CommerceLeague\ActiveCampaign\Logger\Logger;
use CommerceLeague\ActiveCampaign\MessageQueue\AbstractConsumer;
use CommerceLeague\ActiveCampaign\MessageQueue\ConsumerInterface;
use CommerceLeague\ActiveCampaignApi\Exception\HttpException;
use CommerceLeague\ActiveCampaignApi\Exception\UnprocessableEntityHttpException;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\Subscriber;

/**
 * Class ExportContactConsumer
 */
class ExportContactConsumer extends AbstractConsumer implements ConsumerInterface
{

    /**
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * @param SubscriberFactory          $subscriberFactory
     */
    public function __construct(
        SubscriberFactory $subscriberFactory,
        private readonly ContactRepositoryInterface $contactRepository,
        private readonly ContactRequestBuilder $contactRequestBuilder,
        private readonly Client $client,
        private readonly ManagerInterface $eventManager,
        Logger $logger
    ) {
        parent::__construct($logger);
        $this->subscriberFactory     = $subscriberFactory;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function consume(string $message): void
    {
        $message = json_decode($message, true, 512, JSON_THROW_ON_ERROR);

        /** @var Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber = $subscriber->load($message['email'], 'subscriber_email');

        if ($subscriber->getId() === 0) {
            $this->getLogger()->error(__('The Subscriber with the "%1" email doesn\'t exist', $message['email']));
            return;
        }

        $contact = $this->contactRepository->getOrCreateByEmail($subscriber->getEmail());
        $request = $this->contactRequestBuilder->buildWithSubscriber($subscriber);

        try {
            $apiResponse = $this->client->getContactApi()->upsert(['contact' => $request]);
            $contact->setActiveCampaignId($apiResponse['contact']['id']);
            $this->contactRepository->save($contact);

            // trigger event after contact has been saved
            $this->eventManager->dispatch(
                'commmerceleague_activecampaign_export_newsletter_subscriber_success',
                ['contact' => $contact]
            );
        } catch (UnprocessableEntityHttpException $e) {
            $this->logUnprocessableEntityHttpException($e, $request);
            return;
        } catch (HttpException $e) {
            $this->logException($e);
            return;
        }


    }

    /**
     * @inheritDoc
     */
    function processDuplicateEntity(array $request, string $key): void
    {
    }
}
