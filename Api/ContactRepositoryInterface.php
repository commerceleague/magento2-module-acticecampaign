<?php
declare(strict_types=1);
/**
 */
namespace CommerceLeague\ActiveCampaign\Api;

use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Newsletter\Model\Subscriber;

/**
 * Interface ContactRepositoryInterface
 */
interface ContactRepositoryInterface
{
    /**
     * @param Data\ContactInterface $contact
     * @return Data\ContactInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\ContactInterface $contact): Data\ContactInterface;

    /**
     * @param int $contactId
     * @return Data\ContactInterface
     */
    public function getById($contactId): Data\ContactInterface;

    /**
     * @param string $email
     * @return Data\ContactInterface
     */
    public function getByEmail($email): Data\ContactInterface;

    /**
     * @param Customer $customer
     * @return Data\ContactInterface
     * @throws CouldNotSaveException
     */
    public function getOrCreateByCustomer(Customer $customer): Data\ContactInterface;

    /**
     * @param Subscriber $subscriber
     * @return Data\ContactInterface
     * @throws CouldNotSaveException
     */
    public function getOrCreateBySubscriber(Subscriber $subscriber): Data\ContactInterface;

    /**
     * @param Data\ContactInterface $contact
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\ContactInterface $contact): bool;

    /**
     * @param int $contactId
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($contactId): bool;
}
