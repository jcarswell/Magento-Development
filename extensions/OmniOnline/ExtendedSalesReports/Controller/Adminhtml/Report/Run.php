<?php
namespace OmniOnline\ExtendedSalesReports\Controller\Adminhtml\Report;
 
class Run extends \Magento\Backend\App\Action
{
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $sortOrderBuilder;
    protected $reportHelper;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Backend\App\Action\Context $context,
        \OmniOnline\ExtendedSalesReports\Helper\Report $reportHelper
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->reportHelper = $reportHelper;
    }

    public function execute() {
        $store = $this->getRequest()->getPostValue('store');
        $category = $this->getRequest()->getPostValue('category');
        $from = date('Y-m-d 00:00:00', strtotime($this->getRequest()->getPostValue('start_date')));
        $to = date('Y-m-d 23:59:59', strtotime($this->getRequest()->getPostValue('end_date')));
        $include_custom = $this->getRequest()->getPostValue('include_custom');
        $now = gmdate('D, d M Y H:i:s');

        header('Expires: Tue, 03 Jul 2001 06:00:00 GMT');
        header('Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate');
        header('Last-Modified: ' . $now . ' GMT');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=extended-report.csv');
        header('Content-Transfer-Encoding: binary');

        $out = fopen('php://output', 'w');
        fwrite($out, $this->reportHelper->generate($store, $category, $from, $to, $include_custom));
        fclose($out);
        exit;
    }
         
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('OmniOnline_ExtendedSalesReports::report');
    }
}
