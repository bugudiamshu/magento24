<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace EngageBay\Marketing\Controller\Adminhtml\Login;

use EngageBay\Marketing\Helper\Data;
use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Zend_Http_Client_Exception;

class Tracks extends Action
{
    /**
     * @var JsonFactory
     */
    protected JsonFactory $resultJsonFactory;

    /**
     *
     * @var Data
     */
    protected Data $helper;

    /**
     *
     * @var Context
     */
    protected Context $context;

    /**
     *
     * @var EngageBayRestAPIHelper
     */
    protected EngageBayRestAPIHelper $engagebayRestApiHelper;

    /**
     * Authorize constructor.
     *
     * @param Context                $context
     * @param JsonFactory            $resultJsonFactory
     * @param Data                   $helper
     * @param EngageBayRestAPIHelper $engagebay_helper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $helper,
        EngageBayRestAPIHelper $engagebay_helper
    ) {
        $this->resultJsonFactory      = $resultJsonFactory;
        $this->helper                 = $helper;
        $this->context                = $context;
        $this->engagebayRestApiHelper = $engagebay_helper;

        parent::__construct($context);
    }

    /**
     * View Page action
     *
     * @return ResultInterface
     * @throws Zend_Http_Client_Exception
     */
    public function execute()
    {
        $result     = $this->resultJsonFactory->create();
        $restApiKey = $this->context->getRequest()->getParam('restApiKey');

        $this->engagebayRestApiHelper->headers['Authorization'] = $restApiKey;

        return $result->setData(['tracks' => $this->engagebayRestApiHelper->getTracks()]);
    }
}
