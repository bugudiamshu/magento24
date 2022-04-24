<?php

namespace EngageBay\Marketing\Block\System\Config;

use Magento\Framework\Exception\LocalizedException;

class Button extends ButtonField
{
    /**
     * Template class location
     *
     * @var string $_template
     */
    protected $_template = 'EngageBay_Marketing::system/config/button.phtml';

    /**
     * Get EngageBay Login Path
     *
     * @return string
     */
    public function getButtonURL(): string
    {
        return $this->getUrl(
            'engagebay/login/authorize',
            [
                'form_key' => $this->getFormKey(),
            ]
        );
    }

    /**
     * Get Tracks
     *
     * @return string
     */
    public function getTracksURL(): string
    {
        return $this->getUrl(
            'engagebay/login/tracks',
            [
                'form_key' => $this->getFormKey(),
            ]
        );
    }

    /**
     * Get EngageBay Login Button HTML
     *
     * @return string
     * @throws LocalizedException
     */
    public function getButtonHTML(): string
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id'    => 'engagebay_login_btn',
                'label' => __('Connect'),
                'class' => 'secondary',
            ]
        );

        return $button->toHtml();
    }
}

