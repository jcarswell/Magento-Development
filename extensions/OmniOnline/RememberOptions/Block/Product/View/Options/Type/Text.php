<?php
namespace OmniOnline\RememberOptions\Block\Product\View\Options\Type;
class Text extends \Magento\Catalog\Block\Product\View\Options\Type\Text
{
    public function getDefaultValue() {
        $default = $this->getProduct()->getPreconfiguredValues()->getData('options/' . $this->getOption()->getId());
        if (empty($default)) {
            $optTitle = $this->getOption()->getTitle();
            if (isset($_SESSION['OmniOnline']['RememberOptions'][$optTitle])) {
                $default = $_SESSION['OmniOnline']['RememberOptions'][$optTitle];
            }
        }
        return $default;
    }
}
