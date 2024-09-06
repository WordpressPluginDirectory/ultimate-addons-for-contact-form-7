; (function ($) {
  'use strict';
  $(document).ready(function () {
    $("#database_submit").click(function (e) {
      e.preventDefault();
      var id = $('#form-id').val();
      if (id != 0 && id != '') {
        var url = database_admin_url.admin_url + '?page=ultimate-addons-db&form_id=' + id;
        window.location.href = url;
      } else {
        alert('Please select a form first');
      }
    });


    // Export as CSV using jquery ajax
    $(".uacf7-db-export-csv").click(function (e) {
      e.preventDefault();
      var $this = $(this);
      var title = $(this).html();
      // get form id form current url param "form_id"
      var form_id = window.location.search.split('form_id=')[1];
      $this.append('...');
      $.ajax({
        url: database_admin_url.ajaxurl,
        type: 'post',
        data: {
          action: 'uacf7_ajax_database_export_csv',
          ajax_nonce: database_admin_url.nonce,
          form_id: form_id,
        },
        success: function (response) {

          if (response.status === true) {
            var blob = new Blob([response.csv]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = '' + response.file_name + '.csv';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

          }

          if (response.status === false) {
            alert(response.message);

          }

          $this.html(title);
        }
      });
    });


    // UACF7 Db View
    $(".uacf7-db-view").click(function (e) {
      e.preventDefault();
      var $this = $(this);
      var id = $(this).data("id");
      $this.html('<img src="' + database_admin_url.plugin_dir_url + 'assets/images/loader.gif" alt="">');
      $.ajax({
        url: database_admin_url.ajaxurl,
        type: 'post',
        data: {
          action: 'uacf7_ajax_database_popup',
          ajax_nonce: database_admin_url.nonce,
          id: id,
        },
        success: function (data) {
          $("#db_view_wrap").html(data);
          $(".uacf7_popup_preview").fadeIn(0);
          $this.html('View');
          $this.closest('tr').removeClass('unread');

        }
      });
    });

    $(".close").click(function () {
      $(".uacf7_popup_preview").fadeOut(10);
      $("#db_view_wrap").html('');
    });


    // Signature image view
    $(document).on('click', '#signature_view_btn', function (e) {
      e.preventDefault();

      $(".signature_view_pops").css({
        "display": "flex",
      });

      var signa = $(".signature_view_pops").html();
      if (!signa) {
        $("<span>The preview has expired. Please reopen the popup.</span>").appendTo(".signature_view_pops");
      }

      setTimeout(function () {
        $(".signature_view_pops").fadeOut(10);
        // $(".signature_view_pops").html('');
      }, 20000);
    });

    $(document).on('click', '.signature_view_pops', function (e) {
      e.preventDefault();
      $(".signature_view_pops").fadeOut(10);
      // $(".signature_view_pops").html('');
    });


  });
})(jQuery);
