var CalloffFrontend = (function () {

    /**
     * @param {Object} options
     * @param {string} options.value
     * @param {string} options.form_type
     * @param {string} options.selector
     * @param {string} options.form
     * @param {Object} options.storefront
     * @param {string} options.storefront.domain
     * @param {string} options.storefront.url
     */
    CalloffFrontend = function (options) {

        if(options.selector) {

            var self = this;

            function display() {
                $.post('calloff/select', {storefront: options.storefront})
                    .done(function (response) {

                        var $wrapper =  $(options.selector);
                        if($wrapper.length === 0) return;

                        $('.calloff_plugin').remove();

                        $wrapper.append(options.form);

                        options['value'] = response.data;
                        self.form[options.form_type](options);
                    });
            }

            display()

            $(document).on('wa_order_form_changed', function () {
                display()
            });
        } else {
            $('#calloff-script').before(options.form);
            this.form[options.form_type](options);
        }
        
    }

    CalloffFrontend.prototype.form = {
        radio: function (options) {
            var $options = $('.calloff_plugin input[type="radio"]');

            $options
            .filter(function () { return $(this).val() == options.value })
            .attr('checked', true);

            $options.on('click', select)

            function select() {
                $.post('calloff/select', {option: $(this).val(), storefront: options.storefront});
            }
        },

        checkbox: function (options) {
            var $checkbox = $('.calloff_plugin input[type="checkbox"]');

            if (options.value === 'yes') $checkbox.attr('checked', true);

            $checkbox.on('click', check);

            function check() {
                var value = $checkbox.is(':checked') ? 'yes' : 'no';

                $.post('calloff/select', {option: value, storefront: options.storefront})
            }

        }
    }

    return CalloffFrontend;
})()