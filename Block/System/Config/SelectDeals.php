<?php

namespace EngageBay\Marketing\Block\System\Config;


use EngageBay\Marketing\Helper\EngageBayRestAPIHelper;
use Magento\Framework\Data\OptionSourceInterface;

class SelectDeals implements OptionSourceInterface
{
    private EngageBayRestAPIHelper $engageBayRestAPIHelper;

    public function __construct(EngageBayRestAPIHelper $engageBayRestAPIHelper)
    {
        $this->engageBayRestAPIHelper = $engageBayRestAPIHelper;
    }

    public function toOptionArray(): array
    {
        $tracks = $this->engageBayRestAPIHelper->getTracks();

        $options = [];

        foreach ($tracks as $track) {
            $option = [
              'value' => $track['id'],
              'label' => $track['name']
            ];
            $options[] = $option;
        }

        return $options;
    }
}
