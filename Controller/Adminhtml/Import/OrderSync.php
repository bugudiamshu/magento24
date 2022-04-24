<?php

namespace EngageBay\Marketing\Controller\Adminhtml\Import;

use EngageBay\Marketing\Helper\Data;
use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use EngageBay\Marketing\Observer\SyncData;
use Exception;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Psr\Log\LoggerInterface;

class OrderSync extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     *
     * @var Data
     */
    protected $helper;

    /**
     *
     * @var Context
     */
    protected $context;

    /**
     *
     * @var WriterInterface
     */
    protected $config;

    /**
     *
     * @var EngageBayRestAPIHelper
     */
    protected $engagebay_helper;

    /**
     *
     * @var AddressRepositoryInterface
     */
    protected $address_repo;

    /**
     *
     * @var SyncData
     */
    protected $sync_data;

    /**
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * OrderSync constructor.
     *
     * @param Context                    $context
     * @param JsonFactory                $resultJsonFactory
     * @param Data                       $helper
     * @param EngageBayRestAPIHelper     $engagebay_helper
     * @param AddressRepositoryInterface $address_repo
     * @param SyncData                   $sync_data
     * @param LoggerInterface            $logger
     * @param CollectionFactory          $collectionFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $helper,
        EngageBayRestAPIHelper $engagebay_helper,
        AddressRepositoryInterface $address_repo,
        SyncData $sync_data,
        LoggerInterface $logger,
        CollectionFactory $collectionFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper            = $helper;
        $this->context           = $context;
        $this->engagebay_helper  = $engagebay_helper;
        $this->address_repo      = $address_repo;
        $this->logger            = $logger;
        $this->sync_data         = $sync_data;
        $this->collectionFactory = $collectionFactory;
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
            $orders = $this->collectionFactory->create();

            if ($orders->count() > 0) {
                $this->sendBulkData($orders);
            }
            $message = "Success";
        } catch (Exception $e) {
            $this->logger->info("Failed Order Sync");
            $this->logger->info($e->getMessage());
            $message = "Failed";
        }

        $data = ['message' => $message];

        return $result->setData($data);
    }

    /**
     * Send Bulk data to EngageBay
     *
     * @param Collection $orders
     *
     * @throws LocalizedException
     */
    public function sendBulkData(Collection $orders)
    {
        $orders_count = $orders->count();
        $batch_count  = 100;
        $order_chunks = $orders_count / $batch_count;
        $divides_zero = $orders_count % $batch_count;
        $batches      = [];
        if ($divides_zero != 0) {
            $order_chunks = (int)$order_chunks + 1;
        }

        for ($j = 1; $j <= $order_chunks; ++$j) {
            $batches[$j] = [];
        }

        $i = 0;
        /**
 * @var OrderInterface $order 
*/
        foreach ($orders as $order) {
            for ($j = 1; $j <= $order_chunks; ++$j) {
                if ($i < $j * $batch_count) {
                    $batch_data                    = [];
                    $batch_data['contact_details'] = $this->sync_data->prepareContactData($order);
                    $batch_data['order_details']   = $this->sync_data->prepareOrderData($order);
                    array_push($batches[$j], $batch_data);
                    break;
                }
            }
            ++$i;
        }

        foreach ($batches as $batch) {
            $this->engagebay_helper->bulkSync($batch, 'ORDERS');
        }
    }
}
