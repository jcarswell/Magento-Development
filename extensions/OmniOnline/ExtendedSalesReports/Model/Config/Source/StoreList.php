<?php
namespace OmniOnline\ExtendedSalesReports\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class StoreList implements ArrayInterface
{
    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function toOptionArray() {
        $arr = $this->toArray();
        $ret = [];
        foreach ($arr as $key => $value) {
            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }
       return $ret;
    }

    public function toArray() {
        $ret = [
            '' => 'All stores'
        ];
        foreach ($this->getStores() as $store) {
            $ret[$store->getId()] = $store->getWebsite()->getName();
        }
        return $ret;
    }

    public function getStores() {
        return $this->storeManager->getStores();
    }
}
