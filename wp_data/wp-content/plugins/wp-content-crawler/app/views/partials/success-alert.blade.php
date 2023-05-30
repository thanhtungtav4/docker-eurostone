@if(isset($_GET["success"]))
    <?php $message = isset($_GET["message"]) && $_GET["message"] ? urldecode($_GET["message"]) : null; ?>
    @include('partials/alert', [
        'type'      =>  $_GET["success"] == 'true' ? 'success' : 'error',
        'message'   =>  $_GET["success"] == 'true' ?
                        ($message ?: _wpcc('Done')) :
                        ($message ?: _wpcc("An error occurred."))
    ])
@endif