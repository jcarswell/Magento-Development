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
                ['label' => __('IT Services'), 'value'=>'0'],
                ['label' => __('Electrical Services'), 'value'=>'1'],
                ['label' => __('QCX Parade'), 'value'=>'2'],
                ['label' => __('QCX Exhibitor'), 'value'=>'3']
            ];
 
    return $this->_options;
 
    }
 }