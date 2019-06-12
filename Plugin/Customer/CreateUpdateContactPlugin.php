<?php
declare(strict_types=1);
/**
 */
namespace CommerceLeague\ActiveCampaign\Plugin\Customer;

use CommerceLeague\ActiveCampaign\Api\ContactRepositoryInterface;
use CommerceLeague\ActiveCampaign\Logger\Logger;
use CommerceLeague\ActiveCampaign\MessageQueue\Contact\CreateUpdatePublisher;
use CommerceLeague\ActiveCampaign\Model\Contact;
use CommerceLeague\ActiveCampaign\Model\ContactFactory;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository as Subject;
use Magento\Framework\DataObject\Copy as ObjectCopyService;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CreateUpdateContactPlugin
 */
class CreateUpdateContactPlugin
{
    /**
     * @var ContactRepositoryInterface
     */
    private $contactRepository;

    /**
     * @var ContactFactory
     */
    private $contactFactory;

    /**
     * @var ObjectCopyService
     */
    protected $objectCopyService;

    /**
     * @var CreateUpdatePublisher
     */
    private $createUpdatePublisher;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param ContactRepositoryInterface $contactRepository
     * @param ContactFactory $contactFactory
     * @param ObjectCopyService $objectCopyService
     * @param CreateUpdatePublisher $createUpdatePublisher
     * @param Logger $logger
     */
    public function __construct(
        ContactRepositoryInterface $contactRepository,
        ContactFactory $contactFactory,
        ObjectCopyService $objectCopyService,
        CreateUpdatePublisher $createUpdatePublisher,
        Logger $logger
    ) {
        $this->contactRepository = $contactRepository;
        $this->contactFactory = $contactFactory;
        $this->objectCopyService = $objectCopyService;
        $this->createUpdatePublisher = $createUpdatePublisher;
        $this->logger = $logger;
    }

    /**
     * @param Subject $subject
     * @param CustomerInterface|AbstractModel $customer
     * @return CustomerInterface
     */
    public function afterSave(Subject $subject, CustomerInterface $customer): CustomerInterface
    {
        try {
            $contact = $this->contactRepository->getByCustomerId($customer->getId());
        } catch (NoSuchEntityException $e) {
            /** @var Contact $contact */
            $contact = $this->contactFactory->create();
        }

        $this->objectCopyService->copyFieldsetToTarget(
            'activecampaign_convert_customer',
            'to_contact',
            $customer,
            $contact
        );

        try {
            $this->contactRepository->save($contact);
            $this->createUpdatePublisher->execute($contact);
        } catch (CouldNotSaveException $e) {
            $this->logger->critical($e);
        }

        return $customer;
    }
}
