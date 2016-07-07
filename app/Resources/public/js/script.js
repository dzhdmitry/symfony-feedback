$(function() {
    var $container = $('#message-form-container');

    $container.on('click', '.action-message-preview', function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');

        if (!$form[0].checkValidity()) {
            $form.find(':submit').click();

            return;
        }

        $container.find('.action-message-preview').tab('show');
    });

    $container.on('click', '.action-message-edit', function (e) {
        e.preventDefault();

        $container.find('.action-message-edit').tab('show');
        $('#message-preview').empty();
        $container.find('.action-message-preview').removeClass("hide");
        $container.find('.action-message-edit').addClass("hide");
    });

    $container.on('show.bs.tab', '.action-message-preview', function() {
        var $form = $(this).closest('form');

        $.ajax({
            url: "messages/preview",
            type: "post",
            data: new FormData($form[0]),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $container.find('.action-message-preview').prop("disabled", true);
            }
        }).done(function(data) {
            if (data.success) {
                $('#message-preview').html(data.html);

                $container.find('.action-message-preview').addClass("hide");
                $container.find('.action-message-edit').removeClass("hide");
            } else {
                $container.html(data.html);
            }
        }).fail(function() {
            console.log(arguments);
        }).always(function() {
            $container.find('.action-message-preview').prop("disabled", false);
        });
    });
});
