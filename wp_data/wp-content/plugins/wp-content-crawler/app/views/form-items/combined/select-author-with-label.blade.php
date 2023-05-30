{{--
    A select element that shows the available authors in the site and a label. See form-items.combined.select-with-label
    for details.
--}}

@include('form-items.combined.select-with-label', [
    'options' => \WPCCrawler\Utils::getAuthors()
])