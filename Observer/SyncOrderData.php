<?php

namespace EngageBay\Marketing\Observer;

use EngageBay\Marketing\Helper\Data;
use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Currency;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface as Logger;
use Zend_Http_Client_Exception;

class SyncOrderData extends SyncData implements ObserverInterface
{
    /**
     *
     * @var Logger
     */
    private Logger $logger;

    /**
     *
     * @var Data
     */
    private Data $_helper;

    /**
     * @var EngageBayRestAPIHelper
     */
    private EngageBayRestAPIHelper $_engageBayRestAPIHelper;

    /**
     * SyncOrderData constructor.
     *
     * @param Logger                 $logger
     * @param EngageBayRestAPIHelper $engageBayRestAPIHelper
     * @param Data                   $helper
     * @param Image                  $imageHelper
     * @param UrlInterface           $url
     * @param Currency               $currency
     */
    public function __construct(
        Logger $logger,
        EngageBayRestAPIHelper $engageBayRestAPIHelper,
        Data $helper,
        Image $imageHelper,
        UrlInterface $url,
        Currency $currency
    ) {
        $this->logger                  = $logger;
        $this->_helper                 = $helper;
        $this->_engageBayRestAPIHelper = $engageBayRestAPIHelper;

        parent::__construct($engageBayRestAPIHelper, $imageHelper, $helper, $url, $currency);
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     *
     * @throws LocalizedException
     * @throws Zend_Http_Client_Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->_helper->isOrdersSyncEnabled()) {
            $order = $observer->getEvent()->getOrder();
            $this->syncOrderDataToEngageBay($order);
        }
    }

    /**
     * Sync Order to EngageBay
     *
     * @param Order $order
     *
     * @return void
     * @throws LocalizedException
     * @throws Zend_Http_Client_Exception
     */
    public function syncOrderDataToEngageBay(Order $order): void
    {
        $contact_data       = $this->prepareContactData($order);
        $contact_properties = $this->prepareContactJson($contact_data);

        $contact_sync = $this->syncContactToEngageBay($contact_properties, $contact_data['email']);

        if ($contact_sync) {
            $contact_id = $contact_sync['id'];
            $order_data = $this->prepareOrderData($order);
            $this->syncOrderToEngageBay($order_data, $contact_id);
            if ($this->_helper->isDealsSyncEnabled()) {
                $ordersHookPayload = $this->prepareOrdersHookPayload(
                    $order_data['billing_email'],
                    $order_data['items'],
                    $order_data['order_id'],
                    $order_data['subject'],
                    $order_data['content']
                );
                $this->_engageBayRestAPIHelper->syncDeals($ordersHookPayload);
            }
        } else {
            $this->logger->info("Error");
        }
    }
}
