<?php

namespace Adeelq\AbandonedCartReminder\Model;

use Adeelq\AbandonedCartReminder\Helper\AbandonedHelper;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\Quote\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Cron\Model\ResourceModel\Schedule\CollectionFactory as CronCollectionFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use DateInterval;
use DateTime;
use DateTimeZone;
use Throwable;

class CronProcessor
{
    /**
     * @var QuoteCollectionFactory
     */
    private QuoteCollectionFactory $quoteCollectionFactory;

    /**
     * @var AbandonedHelper
     */
    private AbandonedHelper $abandonedHelper;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param AbandonedHelper $abandonedHelper
     * @param QuoteCollectionFactory $quoteCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        AbandonedHelper $abandonedHelper,
        QuoteCollectionFactory $quoteCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->abandonedHelper = $abandonedHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @return void
     */
    public function runCronjobTask(): void
    {
        try {
            foreach ($this->storeManager->getStores() as $store) {
                $cartAbandonmentPeriod = $store->getConfig('adeelq_abandoned_configuration/abandoned_cart/duration');
                $supportEmail = $store->getConfig('trans_email/ident_support/email');
                $supportEmailName = $store->getConfig('trans_email/ident_support/name');
                if (empty($cartAbandonmentPeriod) || empty($supportEmail) || empty($supportEmailName)) {
                    return;
                }

                $quoteCollection = $this->getActiveCartsForStore($store, $cartAbandonmentPeriod);
                if ($quoteCollection && $quoteCollection->getSize()) {
                    $this->abandonedHelper->sendEmails($store, $quoteCollection, $supportEmail, $supportEmailName);
                }
            }
        } catch (Throwable $e) {
            $this->abandonedHelper->logError(__METHOD__, $e);
        }
    }

    /**
     * @return QuoteCollection
     */
    private function getQuoteCollection(): QuoteCollection
    {
        return $this->quoteCollectionFactory->create();
    }

    /**
     * @param StoreInterface $store
     * @param string $cartAbandonmentPeriod
     *
     * @return Collection|boolean
     */
    private function getActiveCartsForStore(
        StoreInterface $store,
        string $cartAbandonmentPeriod
    ): bool|Collection {
        try {
            $fromTime = (new dateTime('now', new dateTimeZone('UTC')))
                ->sub(new DateInterval(sprintf('PT%sM', $cartAbandonmentPeriod)));
            $toTime = clone $fromTime;
            $fromTime->sub(new DateInterval('PT5M'));
            $collection = $this->getQuoteCollection()
                ->addFieldToFilter('main_table.store_id', $store->getId())
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('items_count', ['gt' => 0])
                ->addFieldToFilter('customer_email', ['notnull' => true])
                ->addFieldToFilter(
                    'main_table.updated_at',
                    [
                        'from' => $fromTime->format('Y-m-d H:i:s'),
                        'to' => $toTime->format('Y-m-d H:i:s'),
                        'date' => true,
                    ]
                );

            $condition = 'ns.store_id = main_table.store_id AND
                ns.subscriber_email = main_table.customer_email AND
                ns.subscriber_status = 1';
            $consentedConfig = (bool) $store->getConfig('adeelq_abandoned_configuration/abandoned_cart/consented');
            if ($consentedConfig) {
                $collection->join(['ns' => 'newsletter_subscriber'], $condition, ['ns.customer_id']);
            }

            return $collection;
        } catch (Throwable $e) {
            $this->abandonedHelper->logError(__METHOD__, $e);
            return false;
        }
    }
}
