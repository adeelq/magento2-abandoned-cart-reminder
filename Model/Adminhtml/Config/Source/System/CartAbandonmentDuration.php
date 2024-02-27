<?php

namespace Adeelq\AbandonedCartReminder\Model\Adminhtml\Config\Source\System;

use Magento\Framework\Data\OptionSourceInterface;

class CartAbandonmentDuration implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('Disabled')],
            ['value' => '15', 'label' => __('15 Minutes')],
            ['value' => '30', 'label' => __('30 Minutes')],
            ['value' => '60', 'label' => __('1 Hour')],
            ['value' => '120', 'label' => __('2 Hours')],
            ['value' => '240', 'label' => __('4 Hours')],
            ['value' => '360', 'label' => __('6 Hours')],
            ['value' => '720', 'label' => __('12 Hours')],
            ['value' => '1440', 'label' => __('24 Hours')],
        ];
    }
}
