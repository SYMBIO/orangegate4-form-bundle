/**
 * Created by jiri.bazant on 13.11.15.
 */

(function() {
    /**
     * selector of form holder div
     * @type {string}
     */
    var holderSelector = '.orangegate_form_wrapper';

    /**
     * Callback executed on form.submit
     *
     * Executes AJAX submit instead of standard one
     *
     * @param ev
     * @todo fire some callbacks?
     */
    function formSubmitCallback(ev) {
        var form = $(this);

        // make sure submit is not executed more than once
        if (!form.hasClass('disabled')) {
            form.addClass('disabled');
            //todo lock submit?

            // ajax submit
            $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json'
                })
                .done(function(data) {
                    var holder = form.closest(holderSelector);

                    holder.html(data.form);
                    form = holder.find('form');
                    form.each(prepareAjaxForm);
                })
                .always(function() {
                    form.removeClass('disabled');
                    //todo unlock submit?
                })
            ;
        }

        ev.preventDefault();
    }

    /**
     * Function that register callbacks on form
     */
    function prepareAjaxForm() {
        var form = $(this);

        // make sure form events are not executed multiple times for one form
        if (!form.hasClass('orangegate_form_initialized')) {
            form.submit(formSubmitCallback);
            form.addClass('orangegate_form_initialized');
        }
    }

    // on ready logic
    $(function() {
        // register ajax for all forms
        $(holderSelector + ' form').each(prepareAjaxForm);
    });
})();