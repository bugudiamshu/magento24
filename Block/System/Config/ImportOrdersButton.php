<?php

namespace EngageBay\Marketing\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;

class ImportOrdersButton extends ButtonField
{
    /**
     * Template class location
     *
     * @var string $_template
     */
    protected $_template = 'EngageBay_Marketing::system/config/importOrdersButton.phtml';

    /**
     * Get EngageBay Import Orders Path
     *
     * @return string
     */
    public function getButtonURL()
    {
        return $this->getUrl(
            'engagebay/import/OrderSync', [
            'form_key' => $this->getFormKey()
            ]
        );
    }

    /**
     * Get EngageBay Import Orders Button HTML
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
                'id' => 'engagebay_import_orders_button',
                'label' => __('IMPORT ALL ORDERS TO ENGAGEBAY'),
                'class' => 'primary'
            ]
        );
        return $button->toHtml();
    }
}
