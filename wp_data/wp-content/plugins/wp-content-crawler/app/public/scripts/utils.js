/**
 * Format a string like sprintf function of PHP. Example usage:
 * "{0} is dead, but {1} is alive! {0} {2}".format("ASP", "ASP.NET")
 *
 * @see http://stackoverflow.com/a/4673436/2883487
 */
if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined'
                ? args[number]
                : match
                ;
        });
    };
}

/**
 * Flash the background color of an object
 * @param {object} $element Target element
 */
function flashBackground($element) {
    $element.stop()
        .css('transition', 'none') // Remove transition because it causes unwanted animation behaviors
        .css("background-color", "#b8ea84")
        .animate({ backgroundColor: "#FFFFFF"}, 1000, 'swing', function() {
            // Remove the background color and other changed style definitions when the animation is done.
            $element
                .css("background-color", '')
                .css('transition', '')
        });
}

// Define .emulateTransitionEnd function if it is not defined. This is required for tooltip to work. The function's
// code is extracted from Bootstrap.js
if (typeof jQuery.fn.emulateTransitionEnd !== 'function') {
    jQuery.fn.emulateTransitionEnd = function (duration) {
        var called = false;
        var $el = this;
        $(this).one('bsTransitionEnd', function () { called = true });
        var callback = function () { if (!called) $($el).trigger($.support.transition.end) };
        setTimeout(callback, duration);
        return this;
    }
}