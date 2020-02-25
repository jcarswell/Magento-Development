<?php
namespace OmniOnline\ExtendedSalesReports\Controller\Adminhtml\Report;
 
class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute() {
        return $this->resultPageFactory->create();
    }
         
    protected function _isAllowed() {
        return $this->_authorization->isAllowed('OmniOnline_ExtendedSalesReports::report');
    }
}
