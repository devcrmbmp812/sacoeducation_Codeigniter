(function ($) {
    $(function () {
        $("#unsubscribeInvoice").click(function(event) {
            event.preventDefault();
            var link = $(this);
            var user = link.data("user");
            var subscription = link.data("subscription");
            var url = "/user/unsubscribe/" + user + "/" + subscription + "/";
            $.ajax({
                url: url,
                type: "post",
                beforeSend: function() {
                  $(".loader", ".modal").show();
               }
            }).done(function (data) {
                var jsonData = JSON.parse(data);
                if (jsonData !== 'No such subscriptions') {
                    link.prev().html(jsonData);
                    link.hide();
                }
            }).fail(function () {
                console.error(data);
            }).always(function () {
                $(".loader", ".modal").hide();
            });
        });
        $('#newsletter-form input[type=email]').keypress(function(e) {
            if(e.which == 13) {
                e.preventDefault();
                $('form#newsletter-form button').click();
            }
        });
        $('form#newsletter-form button').click(function(event) {
            event.preventDefault();
            var email = encodeURIComponent($('form#newsletter-form input[name=email]').val());
            $.ajax({
                url: '/newsletter/index/' + email,
                type: "post",
                data : {
                    token: $('form#newsletter-form input[name=authenticity_token]').val()
                },
            }).done(function (response) {
                if(response) {
                    $('#newsletter-alert').html(response);
                }
            }).fail(function () {
                console.error('fail');
            }).always(function () {
                console.log('always');
            });
        });
        $('form#newsletter-form input[name=email]').change(function() {
            $('#newsletter-alert').html('');
        });
        // display left menu on frontend when page loaded
        setTimeout(function () {
            $('.front .left.side-menu').css('left', '0px');
        }, 500);
    });
})(jQuery);
