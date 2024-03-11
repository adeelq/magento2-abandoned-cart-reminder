<?php

namespace Adeelq\AbandonedCartReminder\Controller\Adminhtml\Abandoned;

use Adeelq\AbandonedCartReminder\Controller\Adminhtml\Abandoned;
use Adeelq\CoreModule\Controller\Adminhtml\AbstractIndex;

class Index extends AbstractIndex
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = Abandoned::ADMIN_RESOURCE;

    /**
     * @inheritDoc
     */
    protected function getLabelTitle(): string
    {
        return 'Abandoned Cart Reminder: Email Logs';
    }
}
