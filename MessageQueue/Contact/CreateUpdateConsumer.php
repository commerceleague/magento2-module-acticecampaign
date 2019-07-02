<?php
declare(strict_types=1);
/**
 */
namespace CommerceLeague\ActiveCampaign\MessageQueue\Contact;

use CommerceLeague\ActiveCampaign\Api\ContactRepositoryInterface;
use CommerceLeague\ActiveCampaign\Helper\Client as ClientHelper;
use CommerceLeague\ActiveCampaign\Logger\Logger;
use CommerceLeague\ActiveCampaignApi\Exception\HttpException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class CreateUpdateConsumer
 */
class CreateUpdateConsumer
{
    /**
     * @var ContactRepositoryInterface
     */
    private $contactRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ClientHelper
     */
    private $clientHelper;

    /**
     * @param ContactRepositoryInterface $contactRepository
     * @param Logger $logger
     * @param ClientHelper $clientHelper
     */
    public function __construct(
        ContactRepositoryInterface $contactRepository,
        Logger $logger,
        ClientHelper $clientHelper
    ) {
        $this->contactRepository = $contactRepository;
        $this->logger = $logger;
        $this->clientHelper = $clientHelper;
    }

    /**
     * @param CreateUpdateMessage $message
     */
    public function consume(CreateUpdateMessage $message): void
    {
        $contact = $this->contactRepository->getById($message->getEntityId());

        if (!$contact->getId()) {
            $this->logger->error(__('Unable to find contact with id "%1".', $message->getEntityId()));
            return;
        }

        $request = json_decode($message->getSerializedRequest(), true);

        try {
            $apiResponse = $this->clientHelper->getContactApi()->upsert(['contact' => $request]);
        } catch (HttpException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $contact->setActiveCampaignId($apiResponse['contact']['id']);

        try {
            $this->contactRepository->save($contact);
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
