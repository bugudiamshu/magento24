<?php

namespace EngageBay\Marketing\Observer;

use EngageBay\Marketing\Helper\Data;
use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use Magento\Catalog\Helper\Image;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Currency;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class SyncCustomerData extends SyncData implements ObserverInterface
{
    private Data                  $_helper;
    private CustomerFactory       $_customerFactory;
    private StoreManagerInterface $_storeManager;

    /**
     * SyncCustomerData constructor.
     *
     * @param EngageBayRestAPIHelper $engageBayRestAPIHelper EngageBayRest API Helper
     * @param Data                   $helper                 Configuration Data
     * @param CustomerFactory        $customerFactory        Customer Factory
     * @param StoreManagerInterface  $storeManager           Store Manager
     * @param Image                  $imageHelper            Image
     * @param UrlInterface           $url                    Url
     * @param Currency               $currency               Currency
     */
    public function __construct(
        EngageBayRestAPIHelper $engageBayRestAPIHelper,
        Data $helper,
        CustomerFactory $customerFactory,
        StoreManagerInterface $storeManager,
        Image $imageHelper,
        UrlInterface $url,
        Currency $currency
    ) {
        $this->_helper          = $helper;
        $this->_customerFactory = $customerFactory;
        $this->_storeManager    = $storeManager;

        parent::__construct(
            $engageBayRestAPIHelper, $imageHelper, $helper, $url, $currency
        );
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        if ($this->_helper->isContactSyncEnabled()) {

            $customer = $this->getCustomer($observer->getEvent());

            $contact = [
                'first_name'   => $customer->getFirstname(),
                'last_name'    => $customer->getLastname(),
                'email'        => $customer->getEmail(),
                'phone'        => '',
                'address'      => '',
                'company_name' => '',
            ];

            $contact_properties = $this->prepareContactJson($contact);

            $this->syncContactToEngageBay($contact_properties, $contact['email']);
        }
    }

    /**
     * Get Customer from event
     *
     * @param Event $event
     *
     * @return Customer|CustomerInterface
     * @throws LocalizedException
     */
    private function getCustomer(Event $event)
    {
        switch ($event->getName()) {
        case 'customer_account_edited':
            $customer = $this->_customerFactory->create();
            $customer->setWebsiteId($this->_storeManager->getWebsite()->getId());
            /**
             * @var Customer $customer
             */
            $customer = $customer->loadByEmail($event->getData()['email']);
            break;
        case 'customer_register_success':
        case 'adminhtml_customer_save_after':
        default:
            /**
             * @var CustomerInterface $customer
             */
            $customer = $event->getCustomer();
            break;
        }

        return $customer;
    }
}
