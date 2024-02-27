<?php

namespace Adeelq\AbandonedCartReminder\Model\ResourceModel\Abandoned;

use Adeelq\AbandonedCartReminder\Model\AbandonedModel;
use Adeelq\AbandonedCartReminder\Model\ResourceModel\AbandonedResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class AbandonedCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct(): void
    {
        $this->_idFieldName = 'id';
        $this->_init(AbandonedModel::class, AbandonedResource::class);
    }
}
