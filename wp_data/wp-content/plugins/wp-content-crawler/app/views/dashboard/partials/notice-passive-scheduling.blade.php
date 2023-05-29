{{-- Displays a notice when the scheduling is not active. --}}
@if(!\WPCCrawler\Objects\Settings\SettingService::isSchedulingActive())
    @include('partials.alert', [
        'message' => _wpcc('Scheduling is not active. You should activate the scheduling in the general settings page'
                        . ' for the plugin to crawl posts automatically.'),
        'type' => 'warning',
    ])
@endif