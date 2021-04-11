<?php 
namespace REAL\CheckoutSidebar\Model;
 
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;
 
class ConfigProvider implements ConfigProviderInterface
{
    /** 
     * Path to the config node
     */
    const PATH_CMS_BLOCK = 'checkout/cms_block_config/checkout_cms_block';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfiguration;

    /** 
     * @var LayoutInterface
     */
    protected $cmsBlockWidget;

    /** 
     * Init
     * 
     * @param \Magento\Cms\Block\Widget\Block $block
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Cms\Block\Widget\Block $block,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
        )
    {
        $this->scopeConfiguration = $scopeConfig;
        $this->cmsBlockWidget = $block;
    }

    /**
     * return the block Config data
     * 
     * @return array
     */
    public function getConfig()
    {
        $this->cmsBlockWidget->setData('block_id', $this->getBlockConfig());
        $this->cmsBlockWidget->setTemplate('Magento_Cms::widget/static_block/default.phtml');
        return [
            'cms_block' => $this->cmsBlockWidget->toHtml()
        ];
    }

    /**
     * Returns blockId
     * 
     * @return int
     */
    public function getBlockConfig()
    {
        return $this->scopeConfiguration->getValue(
            self::PATH_CMS_BLOCK,
            ScopeInterface::SCOPE_STORE
        );
    }
}