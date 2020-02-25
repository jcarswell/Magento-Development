<?php
namespace OmniOnline\RememberOptions\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckoutCartProductAddAfter implements ObserverInterface
{
    public function __construct() {
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $item = $observer->getData('quote_item');
        if (!empty($item)) {
            $options = $item->getOptions();
            foreach ($options as $option) {
                if ($option->getCode() == 'info_buyRequest') {
                    $value = $option->getValue();
                    if (is_string($value)) {
                        $value = json_decode($value);
                    }
                    if (!empty($value->options)) {
                        $itemOptions = $value->options;
                        $productOptions = $item->getProduct()->getOptions();
                        foreach ($productOptions as $productOption) {
                            $valueToSet = null;
                            if (!empty($itemOptions->{$productOption->getId()})) {
                                switch($productOption->getType()) {
                                    case 'radio':
                                    case 'drop_down':
                                        foreach ($productOption->getValues() as $productOptionValue) {
                                            if ($itemOptions->{$productOption->getId()} == $productOptionValue->getOptionTypeId()) {
                                                $valueToSet = $productOptionValue->getTitle();
                                                break;
                                            }
                                        }
                                        break;
                                    case 'checkbox':
                                    case 'multiple':
                                        $valueToSet = array();
                                        foreach ($productOption->getValues() as $productOptionValue) {
                                            if (in_array($productOptionValue->getOptionTypeId(), $itemOptions->{$productOption->getId()})) {
                                                $valueToSet[] = $productOptionValue->getTitle();
                                            }
                                        }
                                        break;
                                    case 'date':
                                        if (isset($itemOptions->{$productOption->getId()}->date)) {
                                            $valueToSet = array(
                                                'date' => $itemOptions->{$productOption->getId()}->date
                                            );
                                        } else {
                                            $valueToSet = array(
                                                'month' => $itemOptions->{$productOption->getId()}->month,
                                                'day' => $itemOptions->{$productOption->getId()}->day,
                                                'year' => $itemOptions->{$productOption->getId()}->year
                                            );
                                        }
                                        break;
                                    case 'date_time':
                                        if (isset($itemOptions->{$productOption->getId()}->date)) {
                                            $valueToSet = array(
                                                'date' => $itemOptions->{$productOption->getId()}->date,
                                                'hour' => $itemOptions->{$productOption->getId()}->hour,
                                                'minute' => $itemOptions->{$productOption->getId()}->minute,
                                                'day_part' => $itemOptions->{$productOption->getId()}->day_part
                                            );
                                        } else {
                                            $valueToSet = array(
                                                'month' => $itemOptions->{$productOption->getId()}->month,
                                                'day' => $itemOptions->{$productOption->getId()}->day,
                                                'year' => $itemOptions->{$productOption->getId()}->year,
                                                'hour' => $itemOptions->{$productOption->getId()}->hour,
                                                'minute' => $itemOptions->{$productOption->getId()}->minute,
                                                'day_part' => $itemOptions->{$productOption->getId()}->day_part
                                            );
                                        }
                                        break;
                                    case 'field':
                                    case 'area':
                                    default:
                                        $valueToSet = $itemOptions->{$productOption->getId()};
                                        break;
                                }
                            }
                            $_SESSION['OmniOnline']['RememberOptions'][$productOption->getTitle()] = $valueToSet;
                        }
                    }
                }
            }
        }
    }
}
