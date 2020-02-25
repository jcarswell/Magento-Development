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

        $qcx_parade_email = $this->scopeConfig->getValue(
            'service_groups_notification_settings/email_addresses/qcx_parade_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $qcx_exhibit_email = $this->scopeConfig->getValue(
            'service_groups_notification_settings/email_addresses/qcx_exhibitor_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $it_email = $this->scopeConfig->getValue(
            'service_groups_notification_settings/email_addresses/it_services_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $elec_email = $this->scopeConfig->getValue(
            'service_groups_notification_settings/email_addresses/electrical_services_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $store_email = $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $order = $observer->getEvent()->getOrder();
    
        $billingAddress = $order->getBillingAddress();
        $clientInfo = '<h2>Order ID: ' . $order->getIncrementId() . "</h2>\n<h2>Customer</h2>\n<p>";
        $clientInfo .= $billingAddress->getName() . "<br>\n" . $billingAddress->getCompany() . "<br>\n" . $order->getCustomerEmail() . "<br>\n";
        foreach ($billingAddress->getStreet() as $streetAddress) {
            if ($streetAddress) {
                $clientInfo .= $streetAddress . "<br>\n";
            }
        }
        $clientInfo .= $billingAddress->getRegion() . ', ' . $billingAddress->getCountryId() . "<br>\n" . $billingAddress->getPostcode() . "</p>\n<h2>Requested Items</h2>\n";

        $items = $order->getAllItems();
        $orderInfo = array('it' => '', 'elec' => '', 'qcx_parade' => '', 'qcx_exhibit' => '');
        foreach ($items as $item) {
            $itemInfo = '<table><tr><td colspan="2"><strong>' . $item->getName() . "</strong></td></tr>\n";
            $data = $item->getData();
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
            $notifyIds = explode(',',$item->getProduct()->getData('notify_service_group'));
            foreach ($notifyIds as $notifyId) {
                $notifyGroup = $item->getProduct()->getResource()->getAttribute('notify_service_group')->getSource()->getOptionText($notifyId);
                if ($notifyGroup == 'IT Services') {
                    $orderInfo['it'] .= $itemInfo;
                } elseif ($notifyGroup == 'Electrical Services') {
                    $orderInfo['elec'] .= $itemInfo;
                } elseif ($notifyGroup == 'QCX Parade') {
                    $orderInfo['qcx_parade'] .= $itemInfo;
                } elseif ($notifyGroup == 'QCX Exhibitor') {
                    $orderInfo['qcx_exhibit'] .= $itemInfo;
                }
            }
        }

        $headers  = 'From: ' . $store_email . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        if (!empty($orderInfo['it'])) {
            $body = "<h1>IT Services Request</h1>\n" . $clientInfo . $orderInfo['it'];
            mail($it_email, 'There is a new IT Services request', $body, $headers);
        }
        if (!empty($orderInfo['elec'])) {
            $body = "<h1>Electrical Services Request</h1>\n" . $clientInfo . $orderInfo['elec'];
            mail($elec_email, 'There is a new Electrical Services request', $body, $headers);
        }
        if (!empty($orderInfo['qcx_parade'])) {
            $body = "<h1>Electrical Services Request</h1>\n" . $clientInfo . $orderInfo['elec'];
            mail($elec_email, 'There is a new Electrical Services request', $body, $headers);
        }
        if (!empty($orderInfo['qcx_exhibit'])) {
            $body = "<h1>Electrical Services Request</h1>\n" . $clientInfo . $orderInfo['elec'];
            mail($elec_email, 'There is a new Electrical Services request', $body, $headers);
        }
    }
}
