<?php

namespace OmniOnline\NotifyServiceGroups\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * Base Path
     */
    const PATH_BASE = 'notify_service_group/';

    /**
     * EAV Entity Attribute
     */
    const EAV_ENITIY = 'notify_service_group';

    /**
     * Path to group_1
     */
    private static $VALID_GROUPS = ['group_0','group_1','group_2','group_3'];
 
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfiguration;

    /**
     * @var string|null
     */
    protected $group;

    /**
     * @var $scopeConfig \Magento\Framework\App\Config\ScopeConfigInterface
     * @var $string string value for group
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        $group = ""
        )
    {
        $this->$group = $group;
        $this->$scopeConfig = $scopeConfig;
    }

    /**
     * @var $group requested group id
     * 
     * @return array
     */
    public function getConfig($group = "")
    {
        if (!empty($group)) {
            $this->$group = $group;
        }

        if (!in_array($this->$group, self::$VALID_GROUPS, true)) {
            throw new LogicException("Invalid group requested.");
        }

        $data = array("title" => "","email" => "");

        $data["title"] = $this->scopeConfiguration->getValue(
            self::PATH_BASE . $this -> group . '/title',
            ScopeInterface::SCOPE_STORE
        );

        $data["email"] = $this->scopeConfiguration->getValue(
            self::PATH_BASE . $this -> group . '/email',
            ScopeInterface::SCOPE_STORE
        );
        
        return $data;

    }
}