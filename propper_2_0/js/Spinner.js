/* Spinner.js - created by Agus Suhanto
*/
(function($) {

    var w = [];

    $.startSpinner = function(options) {
        $.startSpinner.impl.start(options);
    };

    $.stopSpinner = function() {
        $.startSpinner.impl.stop();
    };

    $.startSpinner.defaults = {
        opacity: 80,
        overlayId: 'spinner-overlay',
        overlayCss: {},
        containerId: 'spinner-container',
        containerCss: {},
        containerHeight: '100px',
        containerWidth: '200px',
        zIndex: 10000
    };

    $.startSpinner.impl = {
        options: null,

        dialog: {},

        start: function(options) {
            this.options = $.extend({}, $.startSpinner.defaults, options);
            this.zIndex = this.options.zIndex;
            w = this.getDimensions();
            this.dialog.overlay = $('<div></div>')
                .attr('id', this.options.overlayId)
                .addClass('spinner-overlay')
                .css($.extend(this.options.overlayCss, {
                    display: 'block',
                    backgroundColor: '#F5F7F8',
                    filter: 'alpha(opacity=90)',
                    opacity: this.options.opacity / 100,
                    height: w[0],
                    width: w[1],
                    position: 'fixed',
                    left: 0,
                    top: 0,
                    zIndex: this.options.zIndex + 1
                }))
                .appendTo('body');

            var ctop = (w[0] / 2) - (parseFloat(this.options.containerHeight) / 2),
                    cleft = (w[1] / 2) - (parseFloat(this.options.containerWidth) / 2);

            this.dialog.container = $('<div><img src="progressbar.gif"></div>')
                .attr('id', this.options.containerId)
                .addClass('spinner-container')
                .css($.extend(this.options.containerCss, {
                    display: 'block',
                    backgroundColor: '#fff',
                    position: 'fixed',
                    width: this.options.containerWidth,
                    height: this.options.containerHeight,
                    left: cleft,
                    top: ctop,
                    border: '4px solid #99ccff',
                    textAlign: 'center',
                    zIndex: this.options.zIndex + 2
                }))
                .appendTo('body');
        },

        stop: function() {
            $('#' + this.options.containerId).remove();
            $('#' + this.options.overlayId).remove();
        },

        getDimensions: function() {
            var win = $(window);
            return [win.height(), win.width()];
        }
    };

})(jQuery);