<?php
namespace OmniOnline\ExtendedSalesReports\Block\Report;
class Index extends \Magento\Backend\Block\Template
{
    protected $storeManager;
    protected $categoryCollectionFactory;
    protected $categoryHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Helper\Category $categoryHelper
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryHelper = $categoryHelper;
    }

    public function getStores() {
        return $this->storeManager->getStores();
    }

    public function getCategories() {
        return $this->categoryCollectionFactory->create()->addAttributeToSelect('*');
    }
}

