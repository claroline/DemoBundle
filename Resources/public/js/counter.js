/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

(function() {
    'use strict';

    $(document).ready(function() {
        window.setInterval(function(){
            refreshCounter();
        }, 1000);

        function refreshCounter() {
            var counter = $('#demo-remaining-time');
            var nextUpdate = counter.attr('data-next-update');
            //getTime returns a millisecond value, that's why we /1000
            var now = Math.round(new Date().getTime() / 1000);
            var waiting = nextUpdate - now;

            //constructors requires a millisecond value, that's why we *1000
            var waitingDate = new Date(waiting * 1000);
            //because for some reason there is always 1 added hour.
            var hours = waitingDate.getHours() - 1;
            var minutes = waitingDate.getMinutes();
            var seconds = waitingDate.getSeconds();

            counter.html(Translator.get('platform:time_remaining', {'hours': hours, 'minutes': minutes, 'seconds': seconds}));
        };

        $('body').on('click', '.remove-demo-counter', function(event) {
            $('.demo-counter').hide();
        });
    })
})();