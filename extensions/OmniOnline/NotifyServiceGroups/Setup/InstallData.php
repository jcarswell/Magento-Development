<?php
namespace OmniOnline\NotifyServiceGroups\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
* @codeCoverageIgnore
*/
class InstallData implements InstallDataInterface
{
    /***
     * @var eavSetupFactory
     */
    private $eavSetupFactory;

    /***
     * Init
     * 
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup ]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENITITY,
            'notify_service_group',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Notify Service Group',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'source' => '\OmniOnline\NotifyServiceGroups\Model\Source\Options',
                'apply_to' => 'simple,grouped,configurable,downloadable,virtual,bundeled'
            ]
        );
    }
}

