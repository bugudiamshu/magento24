<?php


namespace EngageBay\Marketing\Block\System\Config;

use EngageBay\Marketing\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

abstract class ButtonField extends Field
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * ButtonField constructor.
     *
     * @param Context $context
     * @param Data    $helper
     * @param array   $data
     */
    public function __construct(Context $context, Data $helper, array $data = [])
    {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * Render Element
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get Element HTML
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Get Button URL
     *
     * @return mixed
     */
    abstract public function getButtonURL();

    /**
     * Get Button HTML
     *
     * @return mixed
     */
    abstract public function getButtonHTML();

    /**
     * Get Rest API Key
     *
     * @return string
     */
    public function getRestApiKey()
    {
        return $this->helper->getRestApiKey();
    }
}
