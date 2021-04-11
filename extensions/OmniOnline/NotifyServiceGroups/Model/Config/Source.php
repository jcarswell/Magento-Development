<?php
namespace OmniOnline\NotifyServiceGroups\Model\Config\Source;

class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
    * Get all options
    *
    * @return array
    */
    public function getAllOptions()
    {
        $this->_options = [
                ['label' => __('Group 0'), 'value'=>'group_0'],
                ['label' => __('Group 1'), 'value'=>'group_1'],
                ['label' => __('Group 2'), 'value'=>'group_2'],
                ['label' => __('Group 3'), 'value'=>'group_3']
            ];

        return $this->_options;
    }
 }