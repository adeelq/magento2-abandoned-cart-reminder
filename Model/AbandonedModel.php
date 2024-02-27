<?php

namespace Adeelq\AbandonedCartReminder\Model;

use Adeelq\AbandonedCartReminder\Model\ResourceModel\AbandonedResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime;

/**
 * @method int getQuoteId()
 * @method $this setQuoteId(int $value)
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getEmail()
 * @method $this setEmail(string $value)
 * @method int getEmailSent()
 * @method $this setEmailSent(int $value)
 * @method string getCreatedAt()
 * @method $this setCreatedAt(string $value)
 */
class AbandonedModel extends AbstractModel
{
    /**
     * @var DateTime
     */
    protected DateTime $dateTime;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $dateTime
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $dateTime,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
        $this->dateTime = $dateTime;
    }

    /**
     * @inerhitDoc
     */
    protected function _construct(): void
    {
        $this->_init(AbandonedResource::class);
    }

    /**
     * @return $this
     */
    public function beforeSave(): static
    {
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->dateTime->formatDate(true));
        }

        return parent::beforeSave();
    }
}
