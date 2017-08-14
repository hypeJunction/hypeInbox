define(function (require) {

    var elgg = require('elgg');
    var $ = require('jquery');
    var Ajax = require('elgg/Ajax');

    var popup = {
        /**
         * Update inbox badge with unread count
         *
         * @param {int} unread Unread count
         * @returns {void}
         */
        setNewBadge: function (unread) {
            var unread_str = unread;
            if (unread > 99) {
                unread_str = '99+';
            }
            if (unread > 0) {
                $('#inbox-new').text(unread_str).removeClass('hidden');
                $('#inbox-popup-link .elgg-badge').text(unread_str);
            } else {
                $('#inbox-new').text(unread_str).addClass('hidden');
                $('#inbox-popup-link .elgg-badge').text('');
            }
        }
    };

    $(document).on('open', '.elgg-inbox-popup', function () {
        var $loader = $('<div>').addClass('elgg-ajax-loader');
        $('#inbox-messages').html($loader);

        var ajax = new Ajax(false);
        ajax.action('messages/load').done(function (output) {
            $('#inbox-messages').html(output.list);
            popup.setNewBadge(output.unread);
        });
    });

    $(document).ajaxSuccess(function (event, xhr, settings) {
        if (typeof xhr.responseJSON !== 'undefined' && xhr.responseJSON.inbox) {
            popup.setNewBadge(xhr.responseJSON.inbox.unread || 0);
        }
    });
});
