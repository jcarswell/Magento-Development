<?php
namespace REAL\CheckoutSidebar\Block;

class Cart extends \Magento\Framework\View\Element\Template
{
    /** 
     * Path to the config node
     */
    const PATH_CMS_BLOCK = 'checkout/cms_block_config/cart_cms_block';
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfiguration;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory $agreementCollectionFactory
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
        )
    {
        $this->scopeConfiguration = $scopeConfig;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Template, gets called from real Constructor
     * 
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setData('block_id', $this->getBlockConfig());
        $this->setTemplate('Magento_Cms::widget/static_block/default.phtml');
    }

    /**
     * Returns blockId
     * 
     * @return int
     */
    public function getBlockConfig()
    {
        return $this->scopeConfiguration->getValue(
            $this->PATH_CMS_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }
}