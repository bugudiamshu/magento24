<?php

namespace EngageBay\Marketing\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * Config paths for using throughout the code
     */

    const XML_PATH_ENGAGEBAY_USERNAME     = 'engagebay/auth/engagebay_username';
    const XML_PATH_ENGAGEBAY_PASSWORD     = 'engagebay/auth/engagebay_password';
    const XML_PATH_ENGAGEBAY_REST_API_KEY = 'engagebay/auth/engagebay_rest_api_key';
    const XML_PATH_ENGAGEBAY_JS_API_KEY   = 'engagebay/auth/engagebay_js_api_key';
    const XML_PATH_AUTH_STATUS            = 'engagebay/auth/status';
    const XML_PATH_ENGAGEBAY_DOMAIN       = 'engagebay/auth/domain';
    const XML_PATH_WEBPOPUPS              = 'engagebay/settings/engagebay_webpopups';
    const XML_PATH_SYNC_CONTACTS          = 'engagebay/settings/engagebay_sync_contacts';
    const XML_PATH_SYNC_ORDERS            = 'engagebay/settings/engagebay_sync_orders';
    const XML_PATH_SYNC_DEALS             = 'engagebay/settings/engagebay_sync_deals';
    const XML_PATH_SYNC_DEALS_TRACK       = 'engagebay/settings/engagebay_sync_deals_track';

    private WriterInterface $config;

    /**
     * Data constructor.
     *
     * @param Context              $context
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface      $config
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $config
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->config      = $config;
        parent::__construct($context);
    }

    /**
     * Get Username
     *
     * @param string $scope
     *
     * @return string
     */
    public function getUsername($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENGAGEBAY_USERNAME,
            $scope
        );
    }

    /**
     * Get Password
     *
     * @param string $scope
     *
     * @return string
     */
    public function getPassword($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENGAGEBAY_PASSWORD,
            $scope
        );
    }

    /**
     * Get Rest API Key
     *
     * @param string $scope
     *
     * @return string
     */
    public function getRestApiKey($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENGAGEBAY_REST_API_KEY,
            $scope
        );
    }

    /**
     * Get JS API Key
     *
     * @param string $scope
     *
     * @return string
     */
    public function getJsApiKey($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENGAGEBAY_JS_API_KEY,
            $scope
        );
    }

    /**
     * Get Auth Status
     *
     * @param string $scope
     *
     * @return string
     */
    public function getAuthStatus($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_AUTH_STATUS,
            $scope
        );
    }

    /**
     * Get Domain
     *
     * @param string $scope
     *
     * @return string
     */
    public function getDomain($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENGAGEBAY_DOMAIN,
            $scope
        );
    }

    /**
     * Get Domain
     *
     * @param string $scope
     *
     * @return string
     */
    public function getDealsTrackName($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SYNC_DEALS_TRACK,
            $scope
        );
    }

    /**
     * Get if Contacts sync enabled
     *
     * @param string $scope
     *
     * @return string
     */
    public function isContactSyncEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SYNC_CONTACTS,
            $scope
        );
    }

    /**
     * Get if Orders sync enabled
     *
     * @param string $scope
     *
     * @return string
     */
    public function isOrdersSyncEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SYNC_ORDERS,
            $scope
        );
    }

    /**
     * Get if Deals Sync enabled
     *
     * @param string $scope
     *
     * @return string
     */
    public function isDealsSyncEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SYNC_DEALS,
            $scope
        );
    }

    /**
     * Get if Webpopups enabled
     *
     * @param string $scope
     *
     * @return string
     */
    public function isWebpopupsEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_WEBPOPUPS,
            $scope
        );
    }

    /**
     * Set Rest API Key
     *
     * @param string $rest_api_key
     *
     * @return string
     */
    public function setRestApiKey($rest_api_key)
    {
        $this->config->save(
            self::XML_PATH_ENGAGEBAY_REST_API_KEY,
            $rest_api_key
        );
    }

    /**
     * Set JS API Key
     *
     * @param string $js_api_key
     *
     * @return string
     */
    public function setJsApiKey($js_api_key)
    {
        $this->config->save(
            self::XML_PATH_ENGAGEBAY_JS_API_KEY,
            $js_api_key
        );
    }

    /**
     * Set Domain
     *
     * @param string $domain
     *
     * @return string
     */
    public function setDomain(string $domain)
    {
        $this->config->save(
            self::XML_PATH_ENGAGEBAY_DOMAIN,
            $domain
        );
    }

    /**
     * Set Auth Status
     *
     * @param string $status
     */
    public function setAuthStatus(string $status)
    {
        $this->config->save(
            self::XML_PATH_AUTH_STATUS,
            $status
        );
    }
}
