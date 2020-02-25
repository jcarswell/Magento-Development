<?php
namespace OmniOnline\RememberOptions\Block\Product\View\Options\Type;
class Date extends \Magento\Catalog\Block\Product\View\Options\Type\Date
{
    public function getCalendarDateHtml()
    {
        $option = $this->getOption();
        $value = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $option->getId() . '/date');

        // START OMNIONLINE: Get remembered default.
        if (empty($value)) {
            $optTitle = $option->getTitle();
            if (isset($_SESSION['OmniOnline']['RememberOptions'][$optTitle]['date'])) {
                $value = $_SESSION['OmniOnline']['RememberOptions'][$optTitle]['date'];
            }
        }
        // END OMNIONLINE

        $yearStart = $this->_catalogProductOptionTypeDate->getYearStart();
        $yearEnd = $this->_catalogProductOptionTypeDate->getYearEnd();
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        /** Escape RTL characters which are present in some locales and corrupt formatting */
        $escapedDateFormat = preg_replace('/[^MmDdYy\/\.\-]/', '', $dateFormat);
        $calendar = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Date::class
        )->setId(
            'options_' . $this->getOption()->getId() . '_date'
        )->setName(
            'options[' . $this->getOption()->getId() . '][date]'
        )->setClass(
            'product-custom-option datetime-picker input-text'
        )->setImage(
            $this->getViewFileUrl('Magento_Theme::calendar.png')
        )->setDateFormat(
            $escapedDateFormat
         )->setValue(
             $value
         )->setYearsRange(
             $yearStart . ':' . $yearEnd
         );

         return $calendar->getHtml();
    }

    protected function _getHtmlSelect($name, $value = null)
    {
        $option = $this->getOption();

        $this->setSkipJsReloadPrice(1);

        // $require = $this->getOption()->getIsRequire() ? ' required-entry' : '';
        $require = '';
        $select = $this->getLayout()->createBlock(
            \Magento\Framework\View\Element\Html\Select::class
        )->setId(
            'options_' . $this->getOption()->getId() . '_' . $name
        )->setClass(
            'product-custom-option admin__control-select datetime-picker' . $require
        )->setExtraParams()->setName(
            'options[' . $option->getId() . '][' . $name . ']'
        );

        $extraParams = 'style="width:auto"';
        if (!$this->getSkipJsReloadPrice()) {
            $extraParams .= ' onchange="opConfig.reloadPrice()"';
        }
        $extraParams .= ' data-role="calendar-dropdown" data-calendar-role="' . $name . '"';
        $extraParams .= ' data-selector="' . $select->getName() . '"';
        if ($this->getOption()->getIsRequire()) {
            $extraParams .= ' data-validate=\'{"datetime-validation": true}\'';
        }

        $select->setExtraParams($extraParams);
        if ($value === null) {
            $value = $this->getProduct()->getPreconfiguredValues()->getData(
                'options/' . $option->getId() . '/' . $name
            );

            // START OMNIONLINE: Get remembered default.
            if (empty($value)) {
                $optTitle = $option->getTitle();
                if (isset($_SESSION['OmniOnline']['RememberOptions'][$optTitle][$name])) {
                    $value = $_SESSION['OmniOnline']['RememberOptions'][$optTitle][$name];
                }
            }
            // END OMNIONLINE
        }
        if ($value !== null) {
            $select->setValue($value);
        }

        return $select;
    }

}
