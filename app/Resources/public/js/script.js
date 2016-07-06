$(function() {
    var $preview = $('#action-message-preview');
    var $edit = $('#action-message-edit');

    $preview.click(function(e) {
        e.preventDefault();

        var $form = $(this).closest('form');

        if (!$form[0].checkValidity()) {
            $form.find(':submit').click();

            return;
        }

        $preview.tab('show');
    });

    $edit.click(function (e) {
        e.preventDefault();

        $edit.tab('show');
        $('#message-preview').empty();
        $preview.removeClass("hide");
        $edit.addClass("hide");
    });

    $preview.on('show.bs.tab', function() {
        var $form = $(this).closest('form');

        $.ajax({
            url: "messages/preview",
            type: "post",
            data: new FormData($form[0]),
            processData: false,
            contentType: false,
            beforeSend: function() {
                $preview.prop("disabled", true);
            }
        }).done(function(data) {
            $('#message-preview').html(data);
            $preview.addClass("hide");
            $edit.removeClass("hide");
        }).fail(function() {
            console.log(arguments);
        }).always(function() {
            $preview.prop("disabled", false);
        });
    });
});
