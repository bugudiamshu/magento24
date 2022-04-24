<?php

namespace EngageBay\Marketing\Controller\Adminhtml\Import;

use EngageBay\Marketing\Helper\Data;
use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use Exception;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;
use Zend_Http_Client_Exception;

class CustomerSync extends Action
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @var WriterInterface
     */
    protected WriterInterface $config;

    /**
     * @var EngageBayRestAPIHelper
     */
    protected EngageBayRestAPIHelper $engagebay_helper;

    /**
     * @var AddressRepositoryInterface
     */
    protected AddressRepositoryInterface $address_repo;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $customerCollectionFactory;

    /**
     * CustomerSync constructor.
     *
     * @param Context                    $context
     * @param JsonFactory                $resultJsonFactory
     * @param Data                       $helper
     * @param EngageBayRestAPIHelper     $engagebay_helper
     * @param AddressRepositoryInterface $address_repo
     * @param LoggerInterface            $logger
     * @param CollectionFactory          $customerCollectionFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $helper,
        EngageBayRestAPIHelper $engagebay_helper,
        AddressRepositoryInterface $address_repo,
        LoggerInterface $logger,
        CollectionFactory $customerCollectionFactory
    ) {
        $this->resultJsonFactory         = $resultJsonFactory;
        $this->helper                    = $helper;
        $this->context                   = $context;
        $this->engagebay_helper          = $engagebay_helper;
        $this->address_repo              = $address_repo;
        $this->logger                    = $logger;
        $this->customerCollectionFactory = $customerCollectionFactory;

        parent::__construct($context);
    }

    /**
     * View page action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $customers = $this->customerCollectionFactory->create();

            if ($customers->count() > 0) {
                $this->sendBulkData($customers);
            }
            $message = "Success";
        } catch (Exception $e) {
            $this->logger->info("Failed Customer Sync");
            $this->logger->info($e->getMessage());
            $message = "Failed";
        }

        $data = ['message' => $message];

        return $result->setData($data);
    }

    /**
     * Send Bulk data to EngageBay
     *
     * @param Collection $customers
     *
     * @throws Zend_Http_Client_Exception
     */
    public function sendBulkData(Collection $customers): void
    {
        $users_count  = $customers->count();
        $batch_count  = 100;
        $user_chunks  = $users_count / $batch_count;
        $divides_zero = $users_count % $batch_count;
        $batches      = [];
        if ($divides_zero != 0) {
            $user_chunks = (int) $user_chunks + 1;
        }

        for ($j = 1; $j <= $user_chunks; ++ $j) {
            $batches[$j] = [];
        }

        $i = 0;
        /**
         * @var CustomerInterface $customer
         */
        foreach ($customers as $customer) {
            for ($j = 1; $j <= $user_chunks; ++ $j) {
                if ($i < $j * $batch_count) {
                    $customer_billing_address = isset($customer->getAddresses()[0])
                        ? $customer->getAddresses()[0] : null;
                    $contact                  = [
                        'first_name'   => $customer->getFirstname(),
                        'last_name'    => $customer->getLastname(),
                        'email'        => $customer->getEmail(),
                        'phone'        => $customer_billing_address ? $customer_billing_address->getTelephone() : '',
                        'address'      => $customer_billing_address ?
                            $this->prepareBillingAddress($customer_billing_address) : '',
                        'company_name' => $customer_billing_address ?
                            $customer_billing_address->getCompany() : '',
                        'roles'        => '',
                    ];
                    array_push($batches[$j], $contact);
                    break;
                }
            }
            ++ $i;
        }

        foreach ($batches as $batch) {
            $this->engagebay_helper->bulkSync($batch, 'CONTACT');
        }
    }

    /**
     * Prepare Billing Address
     *
     * @param AddressInterface $billing_address
     *
     * @return false|string
     */
    public function prepareBillingAddress(AddressInterface $billing_address)
    {
        $customer_address = [
            'address' => $billing_address->getStreet(),
            'city'    => $billing_address->getCity(),
            'state'   => $billing_address->getRegion(),
            'zip'     => $billing_address->getPostcode(),
            'country' => $billing_address->getCountryId(),
        ];

        return json_encode($customer_address);
    }
}
