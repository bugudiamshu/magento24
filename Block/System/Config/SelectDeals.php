<?php

namespace EngageBay\Marketing\Block\System\Config;

use EngageBay\Marketing\Helper\Data;
use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use Magento\Framework\Data\OptionSourceInterface;
use Zend_Http_Client_Exception;

class SelectDeals implements OptionSourceInterface
{
    protected Data $helper;
    /**
     * @var EngageBayRestAPIHelper
     */
    private EngageBayRestAPIHelper $engageBayRestAPIHelper;

    /**
     * SelectDeals constructor.
     *
     * @param EngageBayRestAPIHelper $engageBayRestAPIHelper
     * @param Data                   $helper
     */
    public function __construct(EngageBayRestAPIHelper $engageBayRestAPIHelper, Data $helper)
    {
        $this->engageBayRestAPIHelper = $engageBayRestAPIHelper;
        $this->helper = $helper;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     * @throws Zend_Http_Client_Exception
     */
    public function toOptionArray(): array
    {
        $options = [];

        if (!is_null($this->helper->getRestApiKey())) {
            $tracks  = $this->engageBayRestAPIHelper->getTracks();

            foreach ($tracks as $track) {
                $option    = [
                    'value' => $track['id'],
                    'label' => $track['name']
                ];
                $options[] = $option;
            }
        }

        return $options;
    }
}
