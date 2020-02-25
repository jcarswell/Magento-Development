<?php
namespace OmniOnline\ExtendedSalesReports\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
 
class Report extends AbstractHelper
{
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $sortOrderBuilder;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    public function generate($store = null, $category = null, $from = null, $to = null, $include_custom = null) {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('status', 'complete', 'eq')
            ->addFilter('created_at', $from, 'gteq')
            ->addFilter('created_at', $to, 'lteq')
            ->addSortOrder($this->sortOrderBuilder->setField('entity_id')->setAscendingDirection()->create());

        if ($store) {
            $searchCriteria->addFilter('store_id', $store);
        }
    
        $orders = $this->orderRepository->getList($searchCriteria->create());

        $headers = array(
            'Order ID',
            'Date',
            'Name',
            'Company',
            'Street Address',
            'City',
            'State/Province',
            'Country',
            'Zip/Country Code',
            'Email Address',
            'Item Name',
            'Item Price',
            'Item Qty'
        );
        $data = array();
        foreach ($orders as $order) {
            $billingAddress = $order->getBillingAddress();
            $orderData = array(
                'Order ID' => $order->getIncrementId(),
                'Date' => $order->getCreatedAt(),
                'Name' => $billingAddress->getName(),
                'Company' => $billingAddress->getCompany(),
                'Street Address' => '',
                'City' => $billingAddress->getCity(),
                'State/Province' => $billingAddress->getRegion(),
                'Country' => $billingAddress->getCountryId(),
                'Zip/Country Code' => $billingAddress->getPostcode(),
                'Email Address' => $billingAddress->getEmail(),
            );
            $address = array();
            foreach ($billingAddress->getStreet() as $addressLine) {
                if ($addressLine) {
                    $address[] = $addressLine;
                }
            }
            $orderData['Street Address'] = implode("\n", $address);
            $items = $order->getAllItems();
            foreach ($items as $item) {
                if (empty($category) || in_array($category, $item->getProduct()->getCategoryIds())) {
                    $itemData = array(
                        'Item Name' => $item->getName(),
                        'Item Price' => $item->getPrice(),
                        'Item Qty' => $item->getQtyOrdered()
                    );
                    if ($include_custom && isset($item->getProductOptions()['options'])) {
                        $options = $item->getProductOptions()['options'];
                        foreach ($options as $option) {
                            if (!in_array($option['label'], $headers)) {
                                $headers[] = $option['label'];
                            }
                            $itemData[$option['label']] = $option['print_value'];
                        }
                    }
                    $data[] = array_merge($orderData, $itemData);
                }
            }
        }

        $out = '';
        $comma = '';
        foreach ($headers as $header) {
            $out .= $comma . '"' . $header . '"';
            $comma = ",";
        }
        foreach ($data as $row) {
            $out .= "\n";
            $comma = '';
            foreach ($headers as $header) {
                $out .= $comma . '"';
                if (!empty($row[$header])) {
                    $out .= $row[$header];
                }
                $out .= '"';
                $comma = ',';
            }
        }

        return $out;
    }
}
