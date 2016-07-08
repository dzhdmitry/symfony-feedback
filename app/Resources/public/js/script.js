(function($) {
    $.fn.applyForm = function(options) {
        var settings = $.extend({}, {
            done: function() {},
            fail: function() {}
        }, options);

        this.each(function(i, container) {
            var $form = $(container),
                $submit = $form.find(':submit');

            $.ajax({
                url: $form.attr("action"),
                type: $form.attr("method"),
                data: $form.serialize(),
                beforeSend: function() {
                    $submit.prop("disabled", true);
                }
            }).done(settings.done).fail(settings.fail).always(function() {
                $submit.prop("disabled", false);
            });
        });

        return this;
    };
})(jQuery);

var DynamicForm = (function() {
    var defaults = {
        container: $(),
        selector: 'form',
        done: function() {},
        fail: function() {},
        open: {
            done: function() {},
            fail: function() {}
        }
    };

    return Backbone.View.extend({
        initialize: function(options) {
            var self = this,
                settings = this.settings = _.extend({}, defaults, options);

            this.setElement(settings.$el);

            this.initial = this.$el.html();

            this.$el.on('submit', settings.selector, function(e) {
                e.preventDefault();
                self.submit(this);
            });
        },
        paste: function(html) {
            this.$el.html(html);
        },
        open: function(url) {
            var self = this;

            $.ajax({
                url: url,
                beforeSend: function() {
                    self.$el.html(self.initial).css("opacity", 0.8);
                }
            }).done(function(data) {
                self.paste(data.html);
            }).fail(function() {
                self.settings.open.fail.call(null, url);
            }).always(function() {
                self.$el.css("opacity", 1);
            });
        },
        submit: function(form) {
            var self = this;

            $(form).applyForm({
                done: function(data) {
                    if (data.success) {
                        self.settings.done(data);
                    } else {
                        self.paste(data.html);
                    }
                },
                fail: self.settings.fail
            });
        }
    });
})();

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
