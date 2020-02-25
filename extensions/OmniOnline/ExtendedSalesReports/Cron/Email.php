<?php
namespace OmniOnline\ExtendedSalesReports\Cron;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {

    protected $scopeConfig;
    protected $reportHelper;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \OmniOnline\ExtendedSalesReports\Helper\Report $reportHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->reportHelper = $reportHelper;
    }

    public function execute() {
        $store_email = $this->scopeConfig->getValue(
            'trans_email/ident_sales/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $to_email = $this->scopeConfig->getValue(
            'extended_sales_reports_notification_settings/extended_sales_reports_weekly/extended_sales_reports_weekly_email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $store = $this->scopeConfig->getValue(
            'extended_sales_reports_notification_settings/extended_sales_reports_weekly/extended_sales_reports_weekly_story',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $category = $this->scopeConfig->getValue(
            'extended_sales_reports_notification_settings/extended_sales_reports_weekly/extended_sales_reports_weekly_category',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $include_custom = $this->scopeConfig->getValue(
            'extended_sales_reports_notification_settings/extended_sales_reports_weekly/extended_sales_reports_weekly_include_custom',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $from = date('Y-m-d', strtotime('-1 day'));
        $to = date('Y-m-d', time());

        $report = $this->reportHelper->generate($store, $category, $from, $to, $include_custom);

        $mail = new PHPMailer();
        $mail->isHTML(false);
        $mail->setFrom($store_email);
        foreach (explode(',',$to_email) as $address) {
            $mail->addAddress($address);
        }
        $mail->Subject = 'Registration sales report for ' . $from . ' through ' .$to;
        $mail->Body = 'Registration sales report for ' . $from . ' through ' .$to . ' attached';
        $mail->addStringAttachment($report, 'report-' . $from . '-' . $to . '.csv');
        $mail->send();
    }
}

