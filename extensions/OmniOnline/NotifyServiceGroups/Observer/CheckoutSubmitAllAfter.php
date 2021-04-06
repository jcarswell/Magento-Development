<?php
namespace OmniOnline\NotifyServiceGroups\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfter implements ObserverInterface
{
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        $store_email = $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $order = $observer->getEvent()->getOrder();
    
        $billingAddress = $order->getBillingAddress();
        $clientInfo = '<h2>Order ID: ' . $order->getIncrementId() . "</h2>\n<h2>Customer</h2>\n<p>";
        $clientInfo .= $billingAddress->getName() . "<br>\n" . $billingAddress->getCompany() . "<br>\n" . $order->getCustomerEmail() . "<br>\n". $billingAddress->getTelephone() . "<br>\n";
        foreach ($billingAddress->getStreet() as $streetAddress) {
            if ($streetAddress) {
                $clientInfo .= $streetAddress . "<br>\n";
            }
        }
        $clientInfo .= $billingAddress->getRegion() . ', ' . $billingAddress->getCountryId() . "<br>\n" . $billingAddress->getPostcode() . "</p>\n<h2>Requested Items</h2>\n";

        $items = $order->getAllItems();
        $orderInfo = array();
        foreach ($items as $item) {
            $notifyIds = explode(',',$item->getProduct()->getData('notify_service_group'));
            $data = $item->getData();
            if (!empty($notifyIds) && !empty($data['product_options']['options']) {
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

        if (!empty($orderInfo['IT Services'])) {
            $email = $this->scopeConfig->getValue(
                'service_groups_notification_settings/email_addresses/it_services_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $body = "<h1>IT Services Request</h1>\n" . $clientInfo . $orderInfo['IT Services'];
            mail($email, 'There is a new IT Services request', $body, $headers);
        }
        if (!empty($orderInfo['Electrical Services'])) {
            $email = $this->scopeConfig->getValue(
                'service_groups_notification_settings/email_addresses/electrical_services_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $body = "<h1>Electrical Services Request</h1>\n" . $clientInfo . $orderInfo['Electrical Services'];
            mail($email, 'There is a new Electrical Services request', $body, $headers);
        }
        if (!empty($orderInfo['QCX Parade'])) {
            $email = $this->scopeConfig->getValue(
                'service_groups_notification_settings/email_addresses/qcx_parade_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $body = "<h1>REAL Kids Summer Camp Registration</h1>\n" . $clientInfo . $orderInfo['QCX Parade'];
            mail($email, 'There is a new REAL Kids Summer Camp Registration', $body, $headers);
        }
        if (!empty($orderInfo['QCX Exhibitor'])) {
            $email = $this->scopeConfig->getValue(
                'service_groups_notification_settings/email_addresses/qcx_exhibitor_email',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $body = "<h1>New Order</h1>\n" . $clientInfo . $orderInfo['QCX Exhibitor'];
            mail($email, 'There is a new order', $body, $headers);
        }
    }
}

