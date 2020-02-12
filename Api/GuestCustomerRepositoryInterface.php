<?php
/**
 */

namespace CommerceLeague\ActiveCampaign\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface GuestCustomerRepositoryInterface
 */
interface GuestCustomerRepositoryInterface
{

    /**
     * @param Data\GuestCustomerInterface $customer
     *
     * @return Data\GuestCustomerInterface
     * @throws CouldNotSaveException
     */
    public function save(Data\GuestCustomerInterface $customer): Data\GuestCustomerInterface;

    /**
     * @param int $entityId
     *
     * @return Data\GuestCustomerInterface
     */
    public function getById($entityId): Data\GuestCustomerInterface;

    /**
     * @param string $email
     *
     * @return Data\GuestCustomerInterface
     */
    public function getByEmail(string $email): Data\GuestCustomerInterface;

    /**
     * @param Data\GuestCustomerInterface $customer
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\GuestCustomerInterface $customer): bool;

    /**
     * @param int $entityId
     *
     * @return bool
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById($entityId): bool;
}