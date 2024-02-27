<?php

namespace Adeelq\AbandonedCartReminder\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Throwable;

class AbandonedResource extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_init('adeelq_abandoned_cart_reminder', 'id');
    }
}
