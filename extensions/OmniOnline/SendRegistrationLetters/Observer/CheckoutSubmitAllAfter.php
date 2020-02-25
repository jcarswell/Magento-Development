<?php
namespace OmniOnline\SendRegistrationLetters\Observer;
use Magento\Framework\Event\ObserverInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use TCPDF;

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

        $items = $order->getAllItems();
        foreach ($items as $item) {
            $sendLetterId = $item->getProduct()->getData('send_pdf_letter');
            $sendLetterType = $item->getProduct()->getResource()->getAttribute('send_pdf_letter')->getSource()->getOptionText($sendLetterId);
            if ($sendLetterType == 'International Visitor Registration') {
                $billingAddress = $order->getBillingAddress();

                $to = $billingAddress->getEmail();
                $subject = $this->scopeConfig->getValue(
                    'pdf_letter_templates_international/pdf_letter_templates_international_email/pdf_letter_templates_international_email_subject',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $message = $this->scopeConfig->getValue(
                    'pdf_letter_templates_international/pdf_letter_templates_international_email/pdf_letter_templates_international_email_body',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $bcc = $this->scopeConfig->getValue(
                    'pdf_letter_templates_international/pdf_letter_templates_international_email/pdf_letter_templates_international_email_bcc',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $filename = $this->scopeConfig->getValue(
                    'pdf_letter_templates_international/pdf_letter_templates_international_pdf/pdf_letter_templates_international_pdf_filename',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                $template = $this->scopeConfig->getValue(
                    'pdf_letter_templates_international/pdf_letter_templates_international_pdf/pdf_letter_templates_international_pdf_template',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );

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
                $message = str_ireplace(array_keys($subs), array_values($subs), $message);
                $template = str_ireplace(array_keys($subs), array_values($subs), $template);

                $pdf = new TCPDF('P', 'mm', 'USLETTER', true, 'UTF-8', false);
                $pdf->SetMargins(25, 10, 25);
                $pdf->SetAutoPageBreak(false);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
                $pdf->AddPage();
                $pdf->WriteHtml($template);
                $content = $pdf->Output(null, 'S');

                $mail = new PHPMailer();
                $mail->isHTML(true);
                $mail->setFrom($store_email);
                $mail->addAddress($to);
                if (!empty($bcc)) {
                    $mail->addBCC($bcc);
                }
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->addStringAttachment($content, $filename);
                $mail->send();
            }
        }
    }
}
