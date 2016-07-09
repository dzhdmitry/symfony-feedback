var MessageForm = Backbone.View.extend({
    events: {
        'click button.action-message-preview': "preview",
        'click button.action-message-edit': "edit"
    },
    preview: function(e) {
        e.preventDefault();

        var self = this,
            $btn = $(e.currentTarget),
            $form = $btn.closest('form'),
            form = $form.get(0);

        if (form.checkValidity != undefined && !form.checkValidity()) {
            $form.find(':submit').eq(0).click();

            return;
        }

        $.ajax({
            url: self.$el.data("url"),
            type: "post",
            data: new FormData($form.get(0)),
            processData: false,
            contentType: false,
            beforeSend: function() {
                self.$('button').prop("disabled", true);
            }
        }).done(function(data) {
            if (data.success) {
                self.$('div.tab-message-preview').html(data.html);
                $btn.tab('show');
            } else {
                self.$el.html(data.html);
            }
        }).fail(function() {
            $.notify({
                message: 'Could not open preview'
            }, {
                type: 'warning'
            });
        }).always(function() {
            self.$('button').prop("disabled", false);
        });
    },
    edit: function(e) {
        e.preventDefault();

        $(e.currentTarget).tab('show');
    }
});

$(function() {
    $(document).on('change', 'input.file-label-name', function() {
        var id = $(this).attr("id"),
            files = _.toArray(this.files),
            names = _.pluck(files, "name").join(", ");

        $('[data-for="#' + id + '"]').val(names);
    });
});
