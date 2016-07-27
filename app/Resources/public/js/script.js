(function($) {
    var defaults = {
        container: ""
    };

    function readURL(input, onLoad) {
        if (input.files) {
            for (var i = 0; i < input.files.length; i++) {
                var file = input.files[i];

                if (!file) {
                    continue;
                }

                if (!/\.(png|jpeg|jpg|gif)$/i.test(file.name)) {
                    continue;
                }

                var reader = new FileReader();

                reader.onload = onLoad;

                reader.readAsDataURL(file);
            }
        }
    }

    /**
     * Create preview thumbnail for file input
     *
     * @param {{container}} options
     * @returns {jQuery}
     */
    $.fn.imagePreview = function(options) {
        if (window.FileReader == undefined) {
            return this;
        }

        var settings = $.extend({}, defaults, options);

        settings.container.empty();

        readURL(this.get(0), function(e) {
            var $img = $('<img>')
                .addClass("img-thumbnail img-thumbnail-picture")
                .attr('src', e.target.result);

            settings.container.append($img);
        });

        return this;
    };
})(jQuery);

var MessageForm = Backbone.View.extend({
    events: {
        'click button.action-message-preview': "preview",
        'click button.action-message-edit': "edit"
    },
    initialize: function(options) {
        var defaults = {
            url: "",
            messages: {}
        };

        var messages = {
            preview: "",
            too_large: ""
        };

        var settings = _.extend({}, defaults, options);

        this.url = settings.url;
        this.messages = _.extend({}, messages, settings.messages);
    },
    preview: function(e) {
        e.preventDefault();

        var STATUS_REQUEST_ENTITY_TOO_LARGE = 413,
            self = this,
            $btn = $(e.currentTarget),
            $form = $btn.closest('form'),
            form = $form.get(0);

        if (form.checkValidity != undefined && !form.checkValidity()) {
            $form.find(':submit').eq(0).click();

            return;
        }

        $.ajax({
            url: self.url,
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
        }).fail(function(jqXHR) {
            var message = (jqXHR.status == STATUS_REQUEST_ENTITY_TOO_LARGE) ? self.messages.too_large : self.messages.preview;

            $.notify({
                message: message
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
        var $this = $(this),
            id = $this.attr("id"),
            files = _.toArray(this.files),
            names = _.pluck(files, "name").join(", ");

        $('[data-for="#' + id + '"]').val(names);

        $this.imagePreview({
            container: $this.closest('div.form-group').find('div.img-container')
        });
    });
});
