<?php
declare(strict_types=1);
/**
 */
namespace CommerceLeague\ActiveCampaign\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @throws NoSuchEntityException
     */
    public function getById($contactId): Data\ContactInterface;

    /**
     * @param int $customerId
     * @return Data\ContactInterface
     * @throws NoSuchEntityException
     */
    public function getByCustomerId($customerId): Data\ContactInterface;

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
