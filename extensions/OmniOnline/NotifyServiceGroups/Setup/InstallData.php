<?php
namespace OmniOnline\NotifyServiceGroups\Setup

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eacSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContectInterface $context)
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
                'source' => '',
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

