<?php

namespace EngageBay\Marketing\Block;

use EngageBay\Marketing\Helper\Data;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Config extends Template
{
    /**
     *
     * @var Data
     */
    protected $helper;

    /**
     * Config constructor.
     *
     * @param Context $context
     * @param Data    $helper
     */
    public function __construct(Context $context, Data $helper)
    {
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Get Username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->helper->getUsername();
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->helper->getPassword();
    }

    /**
     * Get JS API Key
     *
     * @return string
     */
    public function getJsApiKey()
    {
        return $this->helper->getRestApiKey();
    }

    /**
     * Get Domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->helper->getDomain();
    }

    /**
     * Check if web popups enabled
     *
     * @return string
     */
    public function isWebpopupsEnabled()
    {
        return $this->helper->isWebpopupsEnabled();
    }
}
