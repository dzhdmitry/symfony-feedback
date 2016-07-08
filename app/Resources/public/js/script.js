var MessageForm = Backbone.View.extend({
    events: {
        'click button.action-message-preview': "preview",
        'click button.action-message-edit': "edit"
    },
    preview: function(e) {
        e.preventDefault();

        var self = this,
            $this = $(e.currentTarget),
            $form = $this.closest('form');

        if (!$form.get(0).checkValidity()) {
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
                self.$('#message-preview').html(data.html);
                $this.tab('show');
            } else {
                self.$el.html(data.html);
            }
        }).fail(function() {
            console.log(arguments);
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
