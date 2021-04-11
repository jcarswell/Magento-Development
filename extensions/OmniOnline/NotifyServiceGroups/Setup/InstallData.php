<?php
namespace OmniOnline\NotifyServiceGroups\Setup;

use Magento\Framework\Setup\InstallDataInterface;

/**
* @codeCoverageIgnore
*/
class InstallData implements InstallDataInterface
{
    /**
     * @var eavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     * 
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(
        \Magento\Framework\Setup\InstallDataInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
        )
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENITITY,
            \OmniOnline\NotifyServiceGroups\Model\ConfigProvider::EAV_ENITIY,
            [
                'group' => 'General',
                'type' => 'varchar',
                'label' => 'Notify Service Group',
                'input' => 'select',
                'source' => 'OmniOnline\NotifyServiceGroups\Model\Source\Options',
                'frontend' => '',
                'backend' => '',
                'required' => false,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'visible' => true,
                'is_html_allowed_on_front' => true,
                'visible_on_front' => false
            ]
        );
    }
}

