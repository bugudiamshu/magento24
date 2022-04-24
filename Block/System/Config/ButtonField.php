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
    private Data $helper;

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
    public function render(AbstractElement $element): string
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
    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }

    /**
     * Get Button URL
     *
     * @return string
     */
    abstract public function getButtonURL(): string;

    /**
     * Get Button HTML
     *
     * @return string
     */
    abstract public function getButtonHTML(): string;

    /**
     * Get Rest API Key
     *
     * @return string|null
     */
    public function getRestApiKey(): ?string
    {
        return $this->helper->getRestApiKey();
    }
}
