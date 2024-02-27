<?php

namespace Adeelq\AbandonedCartReminder\Block\Adminhtml;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Block\Items\AbstractItems;
use Throwable;

class CartItems extends AbstractItems
{
    /**
     * @var QuoteRepository
     */
    private QuoteRepository $cartRepository;

    /**
     * @var Escaper
     */
    public Escaper $escaper;

    /**
     * @var CurrencyFactory
     */
    protected CurrencyFactory $currencyFactory;

    /**
     * @param Context $context
     * @param QuoteRepository $cartRepository
     * @param Escaper $escaper
     * @param CurrencyFactory $currencyFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        QuoteRepository $cartRepository,
        Escaper $escaper,
        CurrencyFactory $currencyFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cartRepository = $cartRepository;
        $this->escaper = $escaper;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @return CartInterface|null
     */
    public function getCart(): ?CartInterface
    {
        try {
            return $this->cartRepository->get((int) $this->getData('cart_id'));
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * @param Item $item
     *
     * @return string
     */
    public function getItemPrice(Item $item): string
    {
        return $this->currencyFactory
            ->create()
            ->load($this->getCart()->getCurrency()->getQuoteCurrencyCode())
            ->formatPrecision($item->getRowTotal(), 2);
    }
}
