<?php

namespace EngageBay\Marketing\Observer;

use EngageBay\Marketing\Helper\Data;
use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Framework\Currency;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
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
            $this->syncProductToEngageBayContact($contact_id, $contact_sync['owner_id'], $order);
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

    /**
     * Sync Product to Owner and Contact in EngageBay
     *
     * @param string $contactId
     * @param string $ownerId
     * @param Order  $order
     *
     * @throws Zend_Http_Client_Exception
     */
    public function syncProductToEngageBayContact(string $contactId, string $ownerId, Order $order): void
    {
        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $_product = $item->getProduct();
            $this->createProduct($contactId, $ownerId, $_product);
//            $engageBayProduct = $this->_engageBayRestAPIHelper->searchProduct($_product->getName());
//            if ($engageBayProduct && isset($engageBayProduct['id'])) {
//                $this->updateProduct($engageBayProduct['id'], $contactId, $ownerId, $_product);
//            } else {
//                $this->createProduct($contactId, $ownerId, $_product);
//            }
        }
    }

    /**
     * Add Product to owner
     *
     * @param string $contactId
     * @param string $ownerId
     * @param Product $product
     *
     * @throws Zend_Http_Client_Exception
     */
    public function createProduct(string $contactId, string $ownerId, Product $product)
    {
        $body = $this->prepareProductPropertiesForEngageBay($ownerId, $product);

        $engageBayProduct = $this->_engageBayRestAPIHelper->addProductToOwner($body);

        if ($engageBayProduct && $engageBayProduct['id']) {
            $this->syncProductToContact($contactId, $engageBayProduct);
        }
    }

    /**
     * Update Product in EngageBay
     *
     * @param string  $engageBayProductId
     * @param string  $contactId
     * @param string  $ownerId
     * @param Product $product
     *
     * @throws Zend_Http_Client_Exception
     */
    public function updateProduct(string $engageBayProductId, string $contactId, string $ownerId, Product $product)
    {
        $body = $this->prepareProductPropertiesForEngageBay($ownerId, $product);
        $body['id'] = $engageBayProductId;

        $engageBayProduct = $this->_engageBayRestAPIHelper->updateProductForOwner($body);

        if ($engageBayProduct && $engageBayProduct['id']) {
            $this->syncProductToContact($contactId, $engageBayProduct);
        }
    }

    /**
     * Prepare Product Properties for EngageBay
     *
     * @param string  $ownerId
     * @param Product $product
     *
     * @return array
     */
    public function prepareProductPropertiesForEngageBay(string $ownerId, Product $product): array
    {
        $this->logger->debug('special');
        $this->logger->debug($product->getSpecialPrice());
        $this->logger->debug('normal');
        $this->logger->debug($product->getPrice());

        return [
            'name'          => $product->getName(),
            'description'   => '',
            'image_url'     => $this->getImageUrl($product),
            'owner_id'      => $ownerId,
            'price'         => $product->getPrice(),
            'discount_type' => 'AMOUNT',
            'discount'      => $product->getSpecialPrice() == $product->getPrice()
                ? 0 : $product->getPrice() - $product->getSpecialPrice(),
            'currency'      => $this->getCurrency(),
            'properties'    => []
        ];
    }

    /**
     * Sync Product to Contact
     *
     * @param string $contactId
     * @param array  $engageBayProduct
     *
     * @throws Zend_Http_Client_Exception
     */
    public function syncProductToContact(string $contactId, array $engageBayProduct)
    {
        $body = [
            'productId'    => $engageBayProduct['id'],
            'isSubscribed' => false,
            'subscribedOn' => '',
            'interval'     => '',
            'status'       => '',
            'updatedOn'    => '',
        ];

        $this->_engageBayRestAPIHelper->syncProductToContact($contactId, $body);
    }
}
