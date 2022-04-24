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
    protected Data $helper;

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
    public function getUsername(): string
    {
        return $this->helper->getUsername();
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->helper->getPassword();
    }

    /**
     * Get JS API Key
     *
     * @return string|null
     */
    public function getJsApiKey(): ?string
    {
        return $this->helper->getRestApiKey();
    }

    /**
     * Get Domain
     *
     * @return string
     */
    public function getDomain(): string
    {
        return $this->helper->getDomain();
    }

    /**
     * Check if web popups enabled
     *
     * @return string
     */
    public function isWebpopupsEnabled(): string
    {
        return $this->helper->isWebpopupsEnabled();
    }
}
