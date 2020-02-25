<?php
namespace OmniOnline\SendRegistrationLetters\Controller\Download;
use TCPDF;

class Link extends \Magento\Downloadable\Controller\Download\Link
{
    public function execute() {
        $session = $this->_getCustomerSession();
        $id = $this->getRequest()->getParam('id', 0);

        $linkPurchasedItem = $this->_objectManager->create(
            'Magento\Downloadable\Model\Link\Purchased\Item'
        )->load(
            $id,
            'link_hash'
        );

        $product = $this->_objectManager->create(
            'Magento\Catalog\Model\Product'
        )->load(
            $linkPurchasedItem->getProductId()
        );

        $sendLetterId = $product->getData('send_pdf_letter');
        $sendLetterType = $product->getResource()->getAttribute('send_pdf_letter')->getSource()->getOptionText($sendLetterId);
        if ($sendLetterType == 'International Visitor Registration') {
            $scopeConfig = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
            $filename = $scopeConfig->getValue(
                'pdf_letter_templates_international/pdf_letter_templates_international_pdf/pdf_letter_templates_international_pdf_filename',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $template = $scopeConfig->getValue(
                'pdf_letter_templates_international/pdf_letter_templates_international_pdf/pdf_letter_templates_international_pdf_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $item = $this->_objectManager->create('Magento\Sales\Model\Order\Item')->load($linkPurchasedItem->getOrderItemId());
            $options = $item->getData('product_options')['options'];
            $subs = array();
            foreach ($options as $option) {
                if ($option['option_type'] == 'date') {
                    $subs['['.$option['label'].']'] = date('F jS, Y',strtotime($option['value']));
                } else {
                    $subs['['.$option['label'].']'] = $option['print_value'];
                }
            }
            $subs['[Current Date]'] = date('F jS, Y');
            $template = str_ireplace(array_keys($subs), array_values($subs), $template);

            $pdf = new TCPDF('P', 'mm', 'USLETTER', true, 'UTF-8', false);
            $pdf->SetMargins(25, 10, 25);
            $pdf->SetAutoPageBreak(false);
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);
            $pdf->AddPage();
            $pdf->WriteHtml($template);
            $pdf->Output($filename, 'D');
            exit;
        } else {
            return parent::execute();
        }
    }
}
