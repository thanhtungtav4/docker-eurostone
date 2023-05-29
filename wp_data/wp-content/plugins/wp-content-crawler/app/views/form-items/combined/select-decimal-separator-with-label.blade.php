{{--
    A select element that shows the available decimal separator options with a label. See
    form-items.combined.select-with-label for details.
--}}

@include('form-items.combined.select-with-label', [
    'options' => \WPCCrawler\Utils::getDecimalSeparatorOptionsForSelect(),
])