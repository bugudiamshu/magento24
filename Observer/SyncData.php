<?php

namespace EngageBay\Marketing\Observer;

use EngageBay\Marketing\Helper\Data;
use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Currency;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;
use Zend_Http_Client_Exception;

class SyncData
{
    private EngageBayRestAPIHelper $_engageBayRestAPIHelper;
    private Image                  $_imageHelper;
    private Data                   $_helper;
    private UrlInterface           $_url;
    private Currency               $_currency;

    /**
     * SyncData constructor.
     */
    public function __construct(
        EngageBayRestAPIHelper $engageBayRestAPIHelper,
        Image $imageHelper,
        Data $helper,
        UrlInterface $url,
        Currency $currency
    )
    {
        $this->_engageBayRestAPIHelper = $engageBayRestAPIHelper;
        $this->_imageHelper            = $imageHelper;
        $this->_helper                 = $helper;
        $this->_url                    = $url;
        $this->_currency               = $currency;
    }

    /**
     * Sync Contact
     *
     * @param array  $contact
     * @param string $email
     *
     * @return false|mixed
     */
    public function syncContactToEngageBay(array $contact, string $email)
    {
        $searchContact = $this->_engageBayRestAPIHelper->searchContact($email);

        if ($searchContact) {
            $result = $this->_engageBayRestAPIHelper->updateContact($contact, $searchContact['id']);
        } else {
            $result = $this->_engageBayRestAPIHelper->addContact($contact);
        }

        return $result;
    }

    /**
     * Sync Order
     *
     * @param array  $order
     * @param string $contact_id
     *
     * @throws Zend_Http_Client_Exception
     */
    public function syncOrderToEngageBay(array $order, string $contact_id)
    {
        $notes_result = $this->_engageBayRestAPIHelper->createNotes($order, $contact_id);
        if ($notes_result) {
            $this->_engageBayRestAPIHelper->createTags($order['billing_email'], $order['products']);
        }
    }

    public function prepareOrdersHookPayload($billingEmail, $items, $orderId, $subject, $content): array
    {
        $products = [];
        /**
         * @var OrderItemInterface $item
         */
        foreach ($items as $item) {
            $_product = $item->getProduct();
            array_push(
                $products,
                [
                    'name'       => $item->getName(),
                    'price'      => $item->getPrice(),
                    'quantity'   => $item->getQtyOrdered(),
                    'image'      => $this->_imageHelper->init($_product, 'small_image', ['type' => 'small_image'])->keepAspectRatio(true)->resize('75', '75')->getUrl(),
                    'properties' => [],
                ]
            );
        }

        return [
            'account_domain'                 => $this->_helper->getDomain(),
            'account_api_key'                => $this->_helper->getRestApiKey(),
            'checkout_url'                   => $this->_url->getUrl('checkout'),
            'track_id'                       => $this->_helper->getDealsTrackName(),
            'engagebay_sync_orders_as_deals' => true,
            'order_id'                       => $orderId,
            'subject'                        => $subject,
            'content'                        => $content,
            'currency'                       => $this->_currency->getShortName() . '-' . html_entity_decode($this->_currency->getSymbol(), ENT_COMPAT, 'UTF-8'),
            'contacts'                       => [
                [
                    'email'    => $billingEmail,
                    'products' => $products,
                ],
            ],
        ];
    }

    /**
     * Prepare Contact properties Json
     *
     * @param array $contact
     *
     * @return array
     */
    public function prepareContactJson(array $contact)
    {
        $contact_properties = [
            [
                'name'          => 'name',
                'value'         => $contact['first_name'],
                'field_type'    => 'TEXT',
                'is_searchable' => false,
                'type'          => 'SYSTEM',
            ],
            [
                'name'          => 'last_name',
                'value'         => $contact['last_name'],
                'field_type'    => 'TEXT',
                'is_searchable' => false,
                'type'          => 'SYSTEM',
            ],
            [
                'name'          => 'email',
                'value'         => $contact['email'],
                'field_type'    => 'TEXT',
                'is_searchable' => false,
                'type'          => 'SYSTEM',
            ],
            [
                'name'          => 'phone',
                'value'         => $contact['phone'],
                'field_type'    => 'TEXT',
                'is_searchable' => false,
                'type'          => 'SYSTEM',
                'subtype'       => 'work',
            ],
            [
                'name'          => 'address',
                'value'         => $contact['address'],
                'field_type'    => 'TEXT',
                'is_searchable' => false,
                'type'          => 'SYSTEM',
            ],
        ];

        $tags = [
            ['tag' => 'Magento'],
        ];

        $body_data = [
            'score'        => 10,
            'status'       => 'CONFIRMED',
            'company_name' => $contact['company_name'],
            'properties'   => $contact_properties,
            'tags'         => $tags,
        ];

        return $body_data;
    }

    /**
     * Prepare contact data using Order
     *
     * @param OrderInterface $order
     *
     * @return array
     */
    public function prepareContactData(OrderInterface $order)
    {
        $billing_address = $order->getBillingAddress();
        $is_guest        = $order->getCustomerIsGuest();

        return [
            'first_name'   => $is_guest ? $billing_address->getFirstname() : $order->getCustomerFirstname(),
            'last_name'    => $is_guest ? $billing_address->getLastname() : $order->getCustomerLastname(),
            'email'        => $is_guest ? $billing_address->getEmail() : $order->getCustomerEmail(),
            'phone'        => $billing_address ? $billing_address->getTelephone() : '',
            'address'      => json_encode($this->prepareBillingAddress($billing_address)),
            'company_name' => $billing_address->getCompany(),
            'roles'        => '',
        ];
    }

    /**
     * Prepare Order data
     *
     * @param OrderInterface|Order $order
     *
     * @return array
     * @throws LocalizedException
     */
    public function prepareOrderData(Order $order)
    {
        $order_id      = $order->getId();
        $order_address = $order->getBillingAddress();

        return [
            'order_id'       => $order_id,
            'subject'        => 'Order #' . $order_id . ' has been created',
            'content'        => $this->prepareContent($order),
            'billing_email'  => $order_address->getEmail(),
            'customer_email' => $order->getCustomerIsGuest() ?
                $order_address->getEmail() : $order->getCustomerEmail(),
            'products'       => $this->getItems($order->getItems()),
            'items'          => $order->getItems(),
        ];
    }

    /**
     * Prepare order content
     *
     * @param Order $order
     *
     * @return string
     * @throws LocalizedException
     */
    public function prepareContent(Order $order)
    {
        $content = 'Products: ';
        $content .= $this->getItems($order->getItems()) . '; ';
        $content .= 'Total: ' . $order->getGrandTotal() . '; ';
        $content .= 'Status: ' . $order->getStatusLabel() . '; ';
        $content .= 'Billing Address: ' . implode(', ', array_values($this->prepareBillingAddress($order->getBillingAddress())));

        return $content;
    }

    /**
     * Get order items
     *
     * @param OrderItemInterface[] $items
     *
     * @return string
     */
    public function getItems(array $items)
    {
        $itemNames = [];
        foreach ($items as $item) {
            array_push($itemNames, $item->getName());
        }

        return implode(", ", $itemNames);
    }

    /**
     * Prepare billing address
     *
     * @param OrderAddressInterface $orderBillingAddress
     *
     * @return array
     */
    public function prepareBillingAddress(OrderAddressInterface $orderBillingAddress)
    {
        return [
            'address' => implode(',', $orderBillingAddress->getStreet()),
            'city'    => $orderBillingAddress->getCity(),
            'state'   => $orderBillingAddress->getRegion(),
            'zip'     => $orderBillingAddress->getPostcode(),
            'country' => $orderBillingAddress->getCountryId(),
        ];
    }
}
