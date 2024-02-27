<?php

namespace Adeelq\AbandonedCartReminder\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Abandoned extends Action
{
    /**
     * @inheritDoc
     */
    const ADMIN_RESOURCE = 'Adeelq_AbandonedCartReminder::AbandonedCartReminder';
}
