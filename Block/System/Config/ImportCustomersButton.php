<?php

namespace EngageBay\Marketing\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
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
    public function getButtonURL()
    {
        return $this->getUrl(
            'engagebay/import/CustomerSync', [
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
    public function getButtonHTML()
    {
        $button = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setData(
            [
                'id' => 'engagebay_import_customers_button',
                'label' => __('IMPORT ALL CUSTOMERS TO ENGAGEBAY'),
                'class' => 'primary'
            ]
        );
        return $button->toHtml();
    }
}
