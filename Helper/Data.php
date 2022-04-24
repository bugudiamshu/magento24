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
    public const XML_PATH_ENGAGEBAY_USERNAME = 'engagebay/auth/engagebay_username';
    public const XML_PATH_ENGAGEBAY_PASSWORD = 'engagebay/auth/engagebay_password';
    public const XML_PATH_ENGAGEBAY_REST_API_KEY = 'engagebay/auth/engagebay_rest_api_key';
    public const XML_PATH_ENGAGEBAY_JS_API_KEY = 'engagebay/auth/engagebay_js_api_key';
    public const XML_PATH_AUTH_STATUS = 'engagebay/auth/status';
    public const XML_PATH_ENGAGEBAY_DOMAIN = 'engagebay/auth/domain';
    public const XML_PATH_WEBPOPUPS = 'engagebay/settings/engagebay_webpopups';
    public const XML_PATH_SYNC_CONTACTS = 'engagebay/settings/engagebay_sync_contacts';
    public const XML_PATH_SYNC_ORDERS = 'engagebay/settings/engagebay_sync_orders';
    public const XML_PATH_SYNC_DEALS = 'engagebay/settings/engagebay_sync_deals';
    public const XML_PATH_SYNC_DEALS_TRACK = 'engagebay/settings/engagebay_sync_deals_track';

    /**
     * @var WriterInterface
     */
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
    ) {
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
    public function getUsername(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
    public function getPassword(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
     * @return string|null
     */
    public function getRestApiKey(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
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
    public function getJsApiKey(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
    public function getAuthStatus(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
    public function getDomain(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
     * @return string|null
     */
    public function getDealsTrackName(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): ?string
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
    public function isContactSyncEnabled(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
    public function isOrdersSyncEnabled(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
    public function isDealsSyncEnabled(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
    public function isWebpopupsEnabled(string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT): string
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
     * @return void
     */
    public function setRestApiKey(string $rest_api_key): void
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
     * @return void
     */
    public function setJsApiKey(string $js_api_key): void
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
     * @return void
     */
    public function setDomain(string $domain): void
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
     *
     * @return void
     */
    public function setAuthStatus(string $status): void
    {
        $this->config->save(
            self::XML_PATH_AUTH_STATUS,
            $status
        );
    }
}
