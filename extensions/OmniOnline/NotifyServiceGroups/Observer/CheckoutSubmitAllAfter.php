<?php
namespace OmniOnline\NotifyServiceGroups\Observer;

use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfter implements ObserverInterface
{
    /**
     * Scope Configuration 
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config Provider
     * 
     * @var \OmniOnline\NotifyServiceGroups\Model\ConfigProvider
     */
    protected $configProvider;

    /**
     * Init
     * 
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \OmniOnline\NotifyServiceGroups\Model\ConfigProvider $configProvider
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \OmniOnline\NotifyServiceGroups\Model\ConfigProvider $configProvider
        )
    {
        $this->scopeConfig = $scopeConfig;
        $this->configProvider = $configProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {

        $store_email = $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $order = $observer->getEvent()->getOrder();
    
        //Construct clinet information
        $billingAddress = $order->getBillingAddress();
        $clientInfo = '<h2>Order ID: ' . $order->getIncrementId() . "</h2>\n<h2>Customer</h2>\n<p>";
        $clientInfo .= $billingAddress->getName() . "<br>\n" . $billingAddress->getCompany() . "<br>\n" . $order->getCustomerEmail() . "<br>\n". $billingAddress->getTelephone() . "<br>\n";
        foreach ($billingAddress->getStreet() as $streetAddress) {
            if ($streetAddress) {
                $clientInfo .= $streetAddress . "<br>\n";
            }
        }
        $clientInfo .= $billingAddress->getRegion() . ', ' . $billingAddress->getCountryId() . "<br>\n" . $billingAddress->getPostcode() . "</p>\n<h2>Requested Items</h2>\n";

        //Iterate through all purchased itmes and bill the order information data
        $items = $order->getAllItems();
        $orderInfo = array();
        foreach ($items as $item) {
            $notifyIds = $item->getProduct()->getData(
                \OmniOnline\NotifyServiceGroups\Model\ConfigProvider::EAV_ENITIY);
            
            $data = $item->getData();
            if (!empty($notifyIds) && !empty($data['product_options']['options'])) {
                $itemInfo = '<table><tr><td colspan="2"><strong>' . $item->getName() . "</strong></td></tr>\n";
                $itemInfo .= '<tr><td><strong>Quantity:</strong> ' . $data['qty_ordered'] . '</td>';
                $i = 0;
                $specialInstructions = '';
                foreach ($data['product_options']['options'] as $option) {
                    if ($option['label'] == 'Special Instructions') {
                        $specialInstructions = $option['print_value'];
                    } else {
                        if ($i % 2 == 1) {
                            $itemInfo .= "</tr><tr>\n";
                        }
                        $itemInfo .= '<td><strong>' . $option['label'] . ':</strong> ' . $option['print_value'] . '</td>';
                        $i++;
                    }
                }
                if ($i % 2 == 1) {
                    $itemInfo .= '<td>&nbsp;</td>';
                }
                if ($specialInstructions) {
                        $itemInfo .= "</tr>\n<tr><td colspan=\"2\"><strong>Special Instructions:</strong> " . $specialInstructions . '</td>';
                }
                $itemInfo .= "</tr></table>\n<br>\n";
                foreach ($notifyIds as $notifyId) {
                    $notifyGroup = $item->getProduct()->getResource()->getAttribute('notify_service_group')->getSource()->getOptionText($notifyId);
                    if (!empty($notifyGroup) ) {
                        if (!isset($orderInfo[$notifyGroup])) {
                            $orderInfo[$notifyGroup] = "";
                        $orderInfo[$notifyGroup] .= $itemInfo;
                        }
                    }
                }
            }
        }

        $headers  = 'From: ' . $store_email . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        foreach ($orderInfo as $group => $data) {
            $groupConfig = $configProvider->getConfig($group);
            $body = "<h1>" . $groupConfig["title"] . "<h1>\n" . $clientInfo . $data;
            mail($groupConfig["email"],"There is a new " . $groupConfig["Title"], $body, $headers);

        }
    }
}

