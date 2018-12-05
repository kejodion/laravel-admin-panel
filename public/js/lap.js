// datatable settings
$.extend(true, $.fn.dataTable.defaults, {
    autoWidth: false,
    language: {search: '', searchPlaceholder: 'Search', lengthMenu: '_MENU_ per page'},
    lengthMenu: [5, 10, 25, 50, 100, 250, 500],
    pageLength: 25,
    responsive: true,
    stateDuration: 0,
    stateSave: true,
    stateLoadCallback: function (settings, callback) {
        return JSON.parse(localStorage.getItem($(this).attr('id')));
    },
    stateSaveCallback: function (settings, data) {
        localStorage.setItem($(this).attr('id'), JSON.stringify(data));
    },
    drawCallback: function (settings) {
        let api = this.api();

        // fix pagination if saved page is empty
        if (api.page() > 0 && api.rows({page: 'current'}).count() === 0) {
            api.page('previous').state.save();
            location.reload();
        }
    },
    initComplete: function (settings, json) {
        let api = this.api();

        // fix search input to use buttons
        let search_input = $('<input type="search" class="form-control form-control-sm" placeholder="Search">').val(api.search());
        let search_button = $('<button type="button" class="btn btn-sm btn-link text-secondary p-1" title="Search"><i class="far fa-fw fa-search"></i></button>')
            .click(function () {
                api.search(search_input.val()).draw();

                if (search_input.val().length) {
                    search_button.addClass('d-none');
                    clear_button.removeClass('d-none');
                }
                else {
                    search_button.removeClass('d-none');
                    clear_button.addClass('d-none');
                }
            });
        let clear_button = $('<button type="button" class="btn btn-sm btn-link text-secondary p-1" title="Clear"><i class="far fa-fw fa-times-circle"></i></button>')
            .click(function () {
                search_input.val('');
                search_button.click();
            });

        if (api.search().length) {
            search_button.addClass('d-none');
        }
        else {
            clear_button.addClass('d-none');
        }

        $('#' + settings.nTable.id + '_filter input').unbind();
        $('#' + settings.nTable.id + '_filter').html($('<div class="table-search"></div>').append(search_input, search_button, clear_button));

        $(document).keypress(function (event) {
            if (event.which === 13) {
                search_button.click();
            }
        });
    }
});

$(document).ready(function () {

    // flash alert if present on body
    let body = $('body');

    if (body.attr('data-flash-class')) {
        flash(body.attr('data-flash-class'), body.attr('data-flash-message'));
        body.removeAttr('data-flash-class').removeAttr('data-flash-message');
    }

    // toggle sidebar
    $(document).on('click', '.sidebar-toggle', function (event) {
        event.preventDefault();
        $('.wrapper').toggleClass('wrapper-toggled');
    });

    // submit logout form when link clicked
    $(document).on('click', '#logout_link', function (event) {
        event.preventDefault();
        $(this).closest('form').submit();
    });

    // ajax form processing
    $(document).on('click', '[data-ajax-form] [type="submit"]', function () {
        $(this).closest('[data-ajax-form]').find('[data-button-clicked]').removeAttr('data-button-clicked');
        $(this).attr('data-button-clicked', true);
    });

    $(document).on('submit', '[data-ajax-form]', function (event) {
        event.preventDefault();

        let form = $(this);
        let form_data = new FormData(form[0]);
        let button_clicked = form.find('[data-button-clicked]');

        if (form.attr('data-ajax-form') !== 'submitted') {
            // stop additional form submits
            form.attr('data-ajax-form', 'submitted');

            // append value of submit button clicked
            if (button_clicked.attr('name')) {
                form_data.append(button_clicked.attr('name'), button_clicked.val());
            }

            // remove existing alert & invalid field styles
            $('.alert-flash').remove();
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                contentType: false,
                processData: false,
                data: form_data,
                success: function (response) {
                    // redirect to page
                    if (response.hasOwnProperty('redirect')) {
                        $(location).attr('href', response.redirect);
                    }
                    // reload current page
                    if (response.hasOwnProperty('reload_page')) {
                        location.reload();
                    }
                    // reload datatables on page
                    if (response.hasOwnProperty('reload_datatables')) {
                        $($.fn.dataTable.tables()).DataTable().ajax.reload(null, false);
                    }
                    // flash message using class
                    if (response.hasOwnProperty('flash')) {
                        flash(response.flash[0], response.flash[1]);
                    }
                },
                error: function (response) {
                    // flash error message
                    flash('danger', response.responseJSON.message);

                    // show error for each input
                    if (response.responseJSON.hasOwnProperty('errors')) {
                        $.each(response.responseJSON.errors, function (key, value) {
                            let input = $('#' + $.escapeSelector(key));
                            let container = input.closest('.form-group, [class^="col"]');

                            input.addClass('is-invalid');
                            container.append('<div class="invalid-feedback d-block">' + value[0] + '</div>');
                        });
                    }
                }
            });
        }
    });

    // re-enable form submit when ajax complete
    $(document).ajaxComplete(function () {
        $('[data-ajax-form="submitted"]').attr('data-ajax-form', '');
    });

    // remove invalid style on input entry
    $(document).on('keyup change', '.is-invalid', function () {
        $(this).removeClass('is-invalid');
        $(this).closest('.form-group, [class^="col"]').find('.invalid-feedback').remove();
    });

    // confirm action
    $(document).on('click', '[data-confirm]', function (event) {
        if (!confirm($(this).data('confirm').length ? $(this).data('confirm') : 'Are you sure?')) {
            event.preventDefault();
        }
    });

    // hide/show target based on changed value
    $(document).on('change', '[data-show-hide]', function () {
        let element = $(this);
        let target = $(element.data('show-hide'));

        target.addClass('d-none');
        target.each(function () {
            if (element.find(':checked, :selected').data('show') === $(this).data('show')) {
                $(this).removeClass('d-none');
            }
        });
    });

    // show file names in label when selected
    $(document).on('change', '.custom-file-input', function() {
        let files = [];

        for (let i = 0; i < $(this)[0].files.length; i++) {
            files.push($(this)[0].files[i].name);
        }

        $(this).next('.custom-file-label').html(files.join(', '));
    });

    // set user timezone on login
    let auth_user_timezone = $('#auth_user_timezone');

    if (auth_user_timezone.length) {
        auth_user_timezone.val(Intl.DateTimeFormat().resolvedOptions().timeZone);
    }

    // convert textarea to markdown
    let form_control_markdown = $('.form-control-markdown');

    if (form_control_markdown.length) {
        form_control_markdown.each(function () {
            let textarea = this;
            let easymde = new EasyMDE({
                autoDownloadFontAwesome: false,
                element: textarea,
                showIcons: ['code', 'table'],
                spellChecker: false,
                status: false
            });
            easymde.codemirror.on('change', function () {
                $(textarea).trigger('keyup');
            });
        });
    }
});

function flash(alert_class, alert_message) {
    let html = '<div class="alert alert-flash bg-' + alert_class + ' text-light position-fixed mb-0">' + alert_message + '</div>';

    $(html).hide().appendTo('body').fadeIn('fast', function () {
        $(this).delay(3000).fadeOut('fast', function () {
            $(this).remove();
        });
    });
}