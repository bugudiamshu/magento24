<?php

namespace EngageBay\Marketing\Block\System\Config;

use Magento\Framework\Exception\LocalizedException;

class ImportCustomersButton extends ButtonField
{
    /**
     * Template class location
     *
     * @var string $_template
     */
    protected $_template = 'EngageBay_Marketing::system/config/importCustomersButton.phtml';

    /**
     * Get EngageBay Import Customers Path
     *
     * @return string
     */
    public function getButtonURL(): string
    {
        return $this->getUrl(
            'engagebay/import/CustomerSync',
            [
                                               'form_key' => $this->getFormKey()
                                           ]
        );
    }

    /**
     * Get EngageBay Import Customers Button HTML
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
                'id'    => 'engagebay_import_customers_button',
                'label' => __('IMPORT ALL CUSTOMERS TO ENGAGEBAY'),
                'class' => 'primary'
            ]
        );

        return $button->toHtml();
    }
}
