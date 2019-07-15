<?php
declare(strict_types=1);
/**
 */

namespace CommerceLeague\ActiveCampaign\Model\ResourceModel\Quote;

use CommerceLeague\ActiveCampaign\Setup\SchemaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\ResourceModel\Quote\Collection as ExtendCollection;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @codeCoverageIgnore
 */
class Collection extends ExtendCollection
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Snapshot $entitySnapshot
     * @param TimezoneInterface $timezone
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Snapshot $entitySnapshot,
        TimezoneInterface $timezone,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->timezone = $timezone;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $connection,
            $resource
        );
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['ac_order' => $this->_resource->getTable(SchemaInterface::ORDER_TABLE)],
            'ac_order.magento_quote_id = main_table.entity_id',
            ['ac_order.activecampaign_id']
        );

        return $this;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function addAbandonedFilter(): self
    {
        $this->getSelect()->where('main_table.items_count != 0');
        $this->getSelect()->where('main_table.is_active = 1');
        $this->getSelect()->where('main_table.customer_id IS NOT NULL');

        $fromDateTime = $this->timezone->date()
            ->sub(new \DateInterval('PT1H'))
            ->format('Y-m-d H:i:s');

        $this->getSelect()->where('main_table.updated_at <= ?', $fromDateTime);

        return $this;
    }

    /**
     * @param int $quoteId
     * @return Collection
     */
    public function addIdFilter(int $quoteId): self
    {
        $this->getSelect()->where('main_table.entity_id = ?', $quoteId);
        return $this;
    }

    /**
     * @return Collection
     */
    public function addOmittedFilter(): self
    {
        $this->getSelect()->where('ac_order.activecampaign_id IS NULL');
        return $this;
    }
}
