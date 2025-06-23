(function ($) {
    $(document).ready(function () {

        function tablerowDelete(rowData) {
            // Loader 
            $('#loading').css({ display: 'flex' });
            
            $.ajax({
                url: uACF7DP_Pram.ajaxurl,
                type: 'POST',
                data: {
                    action: 'uacf7dp_deleted_table_datas',
                    data_id: rowData.id,
                    cf7_form_id: rowData.cf7_form_id,
                    nonce: uACF7DP_Pram.nonce,
                },
                success: function (response) {
                    $('#loading').hide();
                    // On successful delete, remove the row from the DataTable
                    // table.row(selectedRowIndex).remove().draw();
                    // console.log(response);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                    $('#loading').hide();
                }
            });
        }

        function tablerowPopUp(rowData) {

            // Loader 
            $('#loading').css({ display: 'flex' });

            $.ajax({
                url: uACF7DP_Pram.ajaxurl,
                type: 'POST',
                data: {
                    action: 'uacf7dp_view_table_data',
                    all_data: rowData,
                    cf7_form_id: rowData.cf7_form_id,
                    nonce: uACF7DP_Pram.nonce,
                },
                success: function (response) {
                    $("#db_view_wrap").html(response);
                    $(".uacf7_popup_preview").fadeIn(0);
                    $('#loading').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loading').hide();
                    console.error('AJAX Error:', textStatus, errorThrown);
                }
            });
        }

        function single_view_control(rowData) {
            var url = uACF7DP_Pram.admin_url + '?page=ultimate-addons-db&form_id=' + rowData.cf7_form_id + '&' + 'entries=' + rowData.id;
            window.location.href = url;
        }

        if ($('#uacf7dp-database-tablePro').is(':empty')) {
            $('#uacf7dp_addons_header').find('.uacf7dp_header-form').addClass('table_hasno_data');
            $('#uacf7dp_addons_header').find('.uacf7dp_main-heading').html('Ultra Database Addon');
        }

        var tableInitialized = true;
        // var dataTable;

        function initializeDataTable(response) {
            var jsonData = response;

            // If DataTable is already initialized, destroy it
            if (tableInitialized) {
                // Check if DataTable is already initialized
                if ($.fn.DataTable.isDataTable('#uacf7dp-database-tablePro')) {
                    $('#uacf7dp_table_container').html("");

                    if ($('#uacf7dp_table_container').is(':empty')) {
                        var tableElement = $('<table>').attr('id', 'uacf7dp-database-tablePro');
                        $('#uacf7dp_table_container').append(tableElement);
                    }
                }
            }

            if ($.isEmptyObject(jsonData)) {
                var entrydiv_Wrap = $('#uacf7dp_table_container_wrap').find('.uacf7dp_table_empty');
                entrydiv_Wrap.css({ display: 'flex' });
                entrydiv_Wrap.find('p').text('Please select another form to proceed.');

                // Get the current image src
                var img = entrydiv_Wrap.find('img');
                var currentSrc = img.attr('src');

                // Replace the file name while keeping the base URL
                var newSrc = currentSrc.replace(/[^\/]*$/, 'uacf7dp_empty_data.png');
                img.attr('src', newSrc);

                entrydiv_Wrap.find('p > span').text('No data was found.');

                $('#uacf7dp_addons_header').find('.uacf7dp_header-form').addClass('table_hasno_data');
                $('#uacf7dp_addons_header').find('.uacf7dp_main-heading').html('Ultra Database Addon');

            } else {

                // Collect all unique keys from the jsonData
                var uniqueKeys = new Set();
                Object.values(jsonData).forEach(item => {
                    Object.keys(item).forEach(key => {
                        uniqueKeys.add(key);
                    });
                });


                // Create columns definition for DataTable as an array of strings
                var columns = Array.from(uniqueKeys).map(key => key);

                // Add a new hidden column to track the insertion time
                // columns.push('insertion_time');

                // Prepare data for DataTable, including the insertion time
                var dataArray = Object.values(jsonData).map((item, index) => {
                    var rowData = {};
                    uniqueKeys.forEach(key => {
                        rowData[key] = item[key] ? item[key].toString() : '';  // Fill missing keys with empty values
                    });
                    // rowData['insertion_time'] = index; // Track insertion time by index
                    return rowData;
                });

                // Extract column names from the first item in the array
                // var columns = Object.keys(jsonData[Object.keys(jsonData)[0]]);

                $('#uacf7dp_table_container_wrap').find('.uacf7dp_table_empty').css({ display: 'none' });
                $('#uacf7dp_addons_header').find('.uacf7dp_header-form').removeClass('table_hasno_data');
                $('#uacf7dp_addons_header').find('.uacf7dp_main-heading').html('Database');

                // Action columns logic here to append
                var action_div = $(
                    `<div id="uacf7dp_action-area">
                        <span class="uacf7dp_action_trigger">···</span>
                        <div class="uacf7dp_action_wrapper">
                            <span id="uacf7dp_action_view_btn" class="uacf7dp_action_view" title="Popup View">${icons.viwe}</span>
                            <span id="uacf7dp_action_del_btn" class="uacf7dp_action_del_btn" title="Delete">${icons.delete}</span>
                        </div>
                    </div>`
                );

                columns = columns.filter(column => column !== 'status' && column !== 'cf7_form_id' && column !== 'submit_ip' && column !== 'submit_browser' && column !== 'submit_date' && column !== 'submit_os' && column !== 'submit_time');

                // Define the element to be moved to the beginning
                var elementToMove = 'id';
                // Find the New columns of the element to be moved
                var index = columns.indexOf(elementToMove);

                // If the element is found in the array, move it to the beginning
                if (index !== -1) {
                    // Remove the element from its current position
                    columns.splice(index, 1);

                    // Move the element to the beginning of the array
                    columns.unshift(elementToMove);
                }

                // Initialize DataTable or add new data
                var table = $('#uacf7dp-database-tablePro').DataTable({

                    data: dataArray,

                    columns: [
                        // Add checkbox column
                        {
                            title: "",
                            data: null,
                            class: 'uacf7dp-database-serial',
                            orderable: true,
                            searchable: false,
                            render: function (data, type, row, meta) {
                                return '<input type="checkbox">';
                            }
                        },

                        // Existing columns
                        ...columns.map(function (column) {
                            return { title: column, data: column };
                        }),

                        // Add static action column
                        {
                            title: "Actions",
                            data: null,
                            orderable: false,
                            searchable: false,
                            render: function (data, type, row, meta) {
                                return action_div.prop('outerHTML');
                            }
                        },
                    ],

                    // Sort by id column in descending order
                    // order: [[columns.indexOf('id'), 'desc']],
                    order: [[1, 'desc']],

                    // responsive: true,
                    dom: 'Bfrt<"uacf7_table_bottom"lip>',

                    // 'createState', 'savedStates',
                    buttons: [
                        {
                            text: `${icons.csv} CSV`,
                            action: function (e, dt, node, config) {
                                
                                const params = new URLSearchParams(window.location.search);
                                const form_id = params.get('form_id');
                                const ajax_nonce = uACF7DP_Pram.nonce;

                                $.ajax({
                                    url: uACF7DP_Pram.ajaxurl,
                                    method: 'POST',
                                    data: {
                                        action: 'uacf7_ajax_database_export_csv',
                                        form_id: form_id,
                                        ajax_nonce: ajax_nonce
                                    },
                                    dataType: 'json',
                                    success: function (response) {
                                        if (response.status) {
                                            const csvContent = response.csv;
                                            const fileName = response.file_name + '.csv';
                                    
                                            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                                            const link = document.createElement('a');
                                            const url = window.URL.createObjectURL(blob);
                                            link.href = url;
                                            link.download = fileName;
                                            document.body.appendChild(link);
                                            link.click();
                                            document.body.removeChild(link);
                                            window.URL.revokeObjectURL(url);
                                        } else {
                                            alert(response.message || 'Export failed.');
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        alert('Failed to export CSV: ' + error);
                                    }
                                });
                            }
                        },
                        {
                            extend: 'pdf',
                            text: `${icons.pdf} PDF`,
                            orientation: 'landscape'
                        },
                    ],

                    select: true,

                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search"
                    },

                    tableHover: true,

                    // scrollY: 200,
                    // deferRender: true,
                    // scroller: true,

                    scrollCollapse: true,
                    scrollX: true,
                    scrollY: 300,

                    fixedHeader: true,
                    fixedColumns: {
                        left: 0,
                        right: 1
                    },

                    // colReorder: true,
                    // rowReorder: true,
                });

                // Event listener for when a row is selected
                table.on('select', function (e, dt, type, indexes) {
                    if (type === 'row') {
                        // 'row' means the selection is at the row level
                        var selectedRowIndex = indexes[0];
                        var rowData = table.row(selectedRowIndex).data();
                        var selectedRow = table.row(selectedRowIndex).node();

                        // var action_trigger = $(selectedRow).find('#uacf7dp_action-area').find('.uacf7dp_action_trigger');
                        var action_trigger = $(selectedRow).find('#uacf7dp_action-area');

                        var viewIcon = $(selectedRow).find('#uacf7dp_action_view_btn');
                        var singleViewIcon = $(selectedRow).find('#uacf7dp_action_single_view_btn');
                        var deleteIcon = $(selectedRow).find('#uacf7dp_action_del_btn');

                        // action_trigger auto open;
                        action_trigger.find('.uacf7dp_action_wrapper').css({
                            'visibility': 'visible',
                            'transform': 'translateX(0px)'
                        });

                        // if (!$(e.target).closest(action_trigger).length) {
                        //     $('.uacf7dp_action_wrapper').css({
                        //         'display': 'none',
                        //         'transform': 'translateX(241px)'
                        //     });
                        // }

                        viewIcon.on('click', function () {
                            tablerowPopUp(rowData);
                        });

                        singleViewIcon.on('click', function () {
                            single_view_control(rowData);
                        });

                        deleteIcon.on('click', function () {
                            // Ask for confirmation before deleting
                            if (confirm(`Are you sure you want to delete this row?`)) {
                                console.log(rowData);
                                // Remove the selected row from the DataTable
                                tablerowDelete(rowData);
                                table.row(selectedRowIndex).remove().draw();
                                // console.log(rowData);
                            }
                        });
                    }
                });

                // Handle row selection events
                table.on('select deselect', function () {
                    var selectedRows = table.rows({ selected: true }).nodes();
                    $('input[type="checkbox"]', selectedRows).prop('checked', true);
                    var deselectedRows = table.rows({ selected: false }).nodes();
                    $('input[type="checkbox"]', deselectedRows).prop('checked', false);

                });

                // Event listener for when a row is deselected
                table.on('deselect', function (e, dt, type, indexes) {
                    if (type === 'row') {
                        var deselectedRowIndex = indexes[0];
                        var deselectedRow = table.row(deselectedRowIndex).node();

                        var action_trigger = $(deselectedRow).find('#uacf7dp_action-area');

                        action_trigger.find('.uacf7dp_action_wrapper').css({
                            'visibility': 'hidden',
                            'transform': 'translateX(241px)'
                        });

                        // Add your logic here to remove the delete icon
                        // $(deselectedRow).find('#uacf7dp_action-area').remove();
                    }
                });

                tableInitialized = true;
            }
        }

        // Handle checkbox clicks
        $('#uacf7dp-database-tablePro tbody').on('click', 'input[type="checkbox"]', function () {
            var $checkbox = $(this);
            var rowData = table.row($checkbox.closest('tr')).data();
            if ($checkbox.is(':checked')) {
                table.row($checkbox.closest('tr')).select();
            } else {
                table.row($checkbox.closest('tr')).deselect();
            }
        });

        // Function to make the AJAX request
        function makeAjaxRequest(id, queryString) {
            var urlString = 'form_id=' + id + '&nonce=' + uACF7DP_Pram.nonce;

            // Loader 
            $('#loading').css({ display: 'flex' });

            $.ajax({
                type: 'POST',
                url: uACF7DP_Pram.ajaxurl,
                data: queryString,
                success: function (response) {
                    var newUrl = window.location.href.split('?')[0] + '?page=ultimate-addons-db&' + urlString;
                    var state = { path: newUrl, ajaxData: response.data.data_sorted };
                    window.history.pushState(state, '', newUrl);
                    initializeDataTable(response.data.data_sorted);
                    // Updated selector value at jQuery
                    $("#select_from_submit").val(id);
                    // Hide loader on success
                    $('#loading').hide();
                },
                error: function (error) {
                    console.error(error);
                    // Hide loader on error
                    $('#loading').hide();
                },
            });
        }

        // Function to fetch and display AJAX data upon page reload
        function fetchAndDisplayAjaxData() {
            if (history.state && history.state.ajaxData) {
                initializeDataTable(history.state.ajaxData);
            } else {
                // If there is no AJAX data in the history state, make an AJAX request to fetch it
                var urlParams = new URLSearchParams(window.location.search);
                var formId = urlParams.get('form_id');
                var nonce = urlParams.get('nonce');

                // Call ajax 
                if (formId && nonce && formId !== "0" && nonce !== "") {
                    var queryString = 'action=uacf7dp_get_table_data&form_id=' + formId + '&nonce=' + uACF7DP_Pram.nonce;
                    makeAjaxRequest(formId, queryString);
                }
            }
        }

        // Call the function to fetch and display AJAX data upon page reload
        fetchAndDisplayAjaxData();

        $("#select_from_submit").change(function (e) {
            e.preventDefault();
            var id = $(this).val();

            //Url
            var queryString = 'action=uacf7dp_get_table_data&form_id=' + id + '&nonce=' + uACF7DP_Pram.nonce;
            var urlString = 'form_id=' + id + '&nonce=' + uACF7DP_Pram.nonce;

            // Update the URL
            var newUrl = window.location.href.split('?')[0] + '?page=ultimate-addons-db&' + urlString;
            window.history.pushState({ path: newUrl, ajaxData: null }, '', newUrl);

            if (id != 0) {
                makeAjaxRequest(id, queryString);
            } else {
                window.history.pushState({ path: newUrl, ajaxData: null }, '', window.location.href.split('?')[0] + '?page=ultimate-addons-db');
            }

        });

        // Handle popstate event to restore AJAX data when navigating back or forward
        window.addEventListener('popstate', function (event) {
            if (event.state && event.state.ajaxData) {
                initializeDataTable(event.state.ajaxData);
            }
        });


        // Single page view start
        $('.uacf7dp_head_btn').on('click', function () {
            window.history.back();
        });

        //Accordion
        $('.accordion-button').click(function () {
            var $this = $(this);
            var collapse = $this.closest('.accordion-item').find('.accordion-collapse');

            // Toggle the clicked section
            collapse.slideToggle();
            $this.attr('aria-expanded', collapse.is(':visible') ? 'true' : 'false');

            // Close other sections
            $('.accordion-collapse').not(collapse).slideUp();
            $('.accordion-button').not($this).attr('aria-expanded', 'false');
        });

        // Handle Mail send 
        $('#uacf7dp_entire_reply_mail_send').submit(function (event) {
            event.preventDefault();

            // loader enable
            $('#loadding_Mail').css({ display: 'flex' });

            // Reference to the form element
            var form = this;

            // Collect form data
            var formData = {
                'receiver_email': $('input[name="receiver_email"]').val(),
                'email_subject': $('input[name="email_subject"]').val(),
                'email_message': $('textarea[name="email_message"]').val(),
                'cf7_form_id': $('input[name="cf7_form_id"]').val(),
                'entries_id': $('input[name="entries_id"]').val(),
            };

            // Perform Ajax request
            $.ajax({
                type: 'POST',
                url: uACF7DP_Pram.ajaxurl,
                data: {
                    action: 'uacf7dp_entire_reply_mail',
                    data: formData,
                    nonce: uACF7DP_Pram.nonce,
                },
                success: function (response) {
                    // Reset the form
                    form.reset();
                    // Handle the Ajax response
                    $('#loadding_Mail').hide();
                    // Reload the page
                    location.reload();
                },

                error: function (error) {
                    console.error(error);
                    // Hide loader on error
                    $('#loadding_Mail').hide();
                },
            });
        });

    });
})(jQuery);
