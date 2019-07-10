<?php
declare(strict_types=1);
/**
 */
namespace CommerceLeague\ActiveCampaign\Model\ActiveCampaign;

use CommerceLeague\ActiveCampaign\Api\ContactRepositoryInterface;
use CommerceLeague\ActiveCampaign\Api\Data;
use CommerceLeague\ActiveCampaign\Model\ResourceModel\ActiveCampaign\Contact as ContactResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ContactRepository
 */
class ContactRepository implements ContactRepositoryInterface
{
    /**
     * @var ContactResource
     */
    private $contactResource;

    /**
     * @var ContactFactory
     */
    private $contactFactory;

    /**
     * @param ContactResource $contactResource
     * @param ContactFactory $contactFactory
     */
    public function __construct(
        ContactResource $contactResource,
        ContactFactory $contactFactory
    ) {
        $this->contactResource = $contactResource;
        $this->contactFactory = $contactFactory;
    }

    /**
     * @param Data\ContactInterface|AbstractModel $contact
     * @return Data\ContactInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\ContactInterface $contact): Data\ContactInterface
    {
        try {
            $this->contactResource->save($contact);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $contact;
    }

    /**
     * @inheritDoc
     */
    public function getById($entityId): Data\ContactInterface
    {
        /** @var Contact $contact */
        $contact = $this->contactFactory->create();
        $this->contactResource->load($contact, $entityId);

        return $contact;
    }

    /**
     * @inheritDoc
     */
    public function getByEmail($email): Data\ContactInterface
    {
        /** @var Contact $contact */
        $contact = $this->contactFactory->create();
        $this->contactResource->load($contact, $email, Data\ContactInterface::EMAIL);

        return $contact;
    }

    /**
     * @inheritDoc
     */
    public function getOrCreateByEmail($email): Data\ContactInterface
    {
        $contact = $this->getByEmail($email);

        if (!$contact->getId()) {
            $contact->setEmail($email);
            $this->save($contact);
        }

        return $contact;
    }

    /**
     * @param Data\ContactInterface|AbstractModel $contact
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\ContactInterface $contact): bool
    {
        try {
            $this->contactResource->delete($contact);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__($e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($entityId): bool
    {
        $contact = $this->getById($entityId);

        if (!$contact->getId()) {
            throw new NoSuchEntityException(
                __('The Contact with the "%1" ID doesn\'t exist', $entityId)
            );
        }

        return $this->delete($contact);
    }
}
