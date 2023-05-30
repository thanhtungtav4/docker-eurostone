{{--
    type: can be "success", "warning", "error", "info"
--}}
<div class="wpcc-notice notice notice-{{ isset($type) ? $type : 'info' }} is-dismissible">
    <p>{{ isset($message) ? $message : _wpcc('Done.') }}</p>
    <button type="button" class="notice-dismiss">
        <span class="screen-reader-text">{{ _wpcc('Dismiss this notice.') }}</span>
    </button>
</div>