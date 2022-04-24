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
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Psr\Log\LoggerInterface;
use Zend_Http_Client_Exception;

class Authorize extends Action
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
     * @var WriterInterface
     */
    protected WriterInterface $config;

    /**
     *
     * @var EngageBayRestAPIHelper
     */
    protected EngageBayRestAPIHelper $engagebay_helper;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * Authorize constructor.
     *
     * @param Context                $context
     * @param JsonFactory            $resultJsonFactory
     * @param Data                   $helper
     * @param EngageBayRestAPIHelper $engagebay_helper
     * @param LoggerInterface        $logger
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $helper,
        EngageBayRestAPIHelper $engagebay_helper,
        LoggerInterface $logger
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper            = $helper;
        $this->context           = $context;
        $this->engagebay_helper  = $engagebay_helper;
        $this->logger            = $logger;
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
        $result             = $this->resultJsonFactory->create();
        $engagebay_username = $this->context->getRequest()->getParam('engagebay_username');
        $engagebay_password = $this->context->getRequest()->getParam('engagebay_password');

        $login = $this->engagebay_helper->login($engagebay_username, $engagebay_password);
        if (isset($login['api_key']['rest_API_Key'])) {
            $message = "Authenticated";
            $restApiKey = $login['api_key']['rest_API_Key'];
            $this->helper->setAuthStatus("true");
            $this->helper->setRestApiKey($login['api_key']['rest_API_Key']);
            $this->helper->setJsApiKey($login['api_key']['js_API_Key']);
            $this->helper->setDomain($login['domain_name']);
        } else {
            $message = "Unauthenticated";
            $restApiKey = '';
            $this->helper->setAuthStatus("false");
            $this->helper->setRestApiKey(null);
            $this->helper->setJsApiKey(null);
            $this->helper->setDomain(null);
        }

        $data = ['message' => $message, 'restApiKey' => $restApiKey];

        return $result->setData($data);
    }
}
