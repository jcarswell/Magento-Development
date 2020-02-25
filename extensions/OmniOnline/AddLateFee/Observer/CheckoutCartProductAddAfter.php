<?php
namespace OmniOnline\AddLateFee\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckoutCartProductAddAfter implements ObserverInterface
{
    public function __construct() {
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $item = $observer->getData('quote_item');
        if (!empty($item)) {
            $item = ($item->getParentItem() ? $item->getParentItem() : $item);
            if ($item->getProduct()->getData('late_fee_applies')) {
                $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct())['options'];
                foreach ($options as $option) {
                    if (stristr($option['label'], 'Start Date') && !empty($option['value'])) {
                        $deadline = strtotime($option['value']) - (86400 * $item->getProduct()->getData('late_fee_interval_days'));
                        if ($deadline <= mktime(0,0,0)) {
                            $price = $item->getProduct()->getFinalPrice() * (1 + ($item->getProduct()->getData('late_fee_percentage') / 100));
                            if ($price > 0) {
                                $item->setCustomPrice($price);
                                $item->setOriginalCustomPrice($price);
                                $item->getProduct()->setIsSuperMode(true);
                            }
                        }
                        break;
                    }
                }
            }
        }
    }
}
