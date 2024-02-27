<?php

namespace Adeelq\AbandonedCartReminder\Helper;

use Adeelq\CoreModule\Helper\Base;
use Adeelq\CoreModule\Logger\Logger;
use Magento\Framework\App\Area;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\Store\Api\Data\StoreInterface;
use Adeelq\AbandonedCartReminder\Model\ResourceModel\AbandonedResource;
use Adeelq\AbandonedCartReminder\Model\AbandonedModelFactory;
use Throwable;

class AbandonedHelper extends Base
{
    /**
     * @var TransportBuilder
     */
    private TransportBuilder $transportBuilder;

    /**
     * @var StateInterface
     */
    private StateInterface $inlineTranslation;

    /**
     * @var AbandonedResource
     */
    private AbandonedResource $abandonedResource;

    /**
     * @var AbandonedModelFactory
     */
    private AbandonedModelFactory $abandonedModelFactory;

    /**
     * @param Logger $logger
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $state
     * @param AbandonedResource $abandonedResource
     * @param AbandonedModelFactory $abandonedModelFactory
     */
    public function __construct(
        Logger $logger,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        AbandonedResource $abandonedResource,
        AbandonedModelFactory $abandonedModelFactory
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $state;
        $this->abandonedModelFactory = $abandonedModelFactory;
        $this->abandonedResource = $abandonedResource;
        parent::__construct($logger);
    }

    /**
     * @param StoreInterface $store
     * @param Collection $collection
     * @param $supportEmail
     * @param $supportEmailName
     *
     * @return void
     */
    public function sendEmails(StoreInterface $store, Collection $collection, $supportEmail, $supportEmailName): void
    {
        /** @var Quote $cart */
        foreach ($collection as $cart) {
            try {
                $this->sendEmail($cart, $store, $supportEmail, $supportEmailName);
                $abandonedCartModel = $this->abandonedModelFactory
                    ->create()
                    ->setQuoteId($cart->getId())
                    ->setStoreId($cart->getStoreId())
                    ->setEmail($cart->getCustomerEmail())
                    ->setCustomerId((int) $cart->getCustomerId())
                    ->setEmailSent(1);
                $this->abandonedResource->save($abandonedCartModel);
            } catch (Throwable $e) {
                $this->logError(__METHOD__, $e);
                continue;
            }
        }
    }

    /**
     * @param Quote $cart
     * @param StoreInterface $store
     * @param $supportEmail
     * @param $supportEmailName
     *
     * @return void
     *
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    private function sendEmail(Quote $cart, StoreInterface $store, $supportEmail, $supportEmailName): void
    {
        $this->inlineTranslation->suspend();
        $transport = $this->transportBuilder
            ->setTemplateIdentifier('aq_abandoned_cart_reminder')
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $store->getId()
                ]
            )
            ->setTemplateVars(
                [
                    'store_name' => $store->getName(),
                    'store_email' => $supportEmail,
                    'cart_url' => $store->getUrl('checkout/cart'),
                    'items_count' => $cart->getItemsCount(),
                    'cart_id' => $cart->getId()
                ]
            )
            ->setFromByScope(
                [
                    'email' => $supportEmail,
                    'name' => $supportEmailName,
                    $store->getId()
                ]
            )
            ->addTo($cart->getCustomerEmail())
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
