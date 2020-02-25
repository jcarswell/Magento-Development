<?php
namespace OmniOnline\ExtendedSalesReports\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CategoryList implements ArrayInterface
{
    protected $categoryCollectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
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
            '' => 'All categories'
        ];
        foreach ($this->getCategories() as $category) {
            $ret[$category->getId()] = $category->getName();
        }
        return $ret;
    }

    public function getCategories() {
        return $this->categoryCollectionFactory->create()->addAttributeToSelect('*');
    }
}
