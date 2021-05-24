var CalloffFrontend = (function () {

    /**
     * @param {Object} options
     * @param {string} options.value
     * @param {string} options.form_type
     * @param {Object} options.storefront
     * @param {string} options.storefront.domain
     * @param {string} options.storefront.url
     */
    CalloffFrontend = function (options) {
        this.form[options.form_type](options);
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