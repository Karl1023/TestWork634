jQuery(document).ready(function($) {

    $( "#search-form" ).on('keyup', function() {
        var searchTerm = $("#search-term").val();

      $.ajax({
          url: ajax_object.ajax_url, 
          type: 'POST',
          data: {
              'action':'ajax_request', 
              'term' : searchTerm 
          },
          success:function(data) {
            console.log('AJAX response:', data);
              $("#table-body").html(data);
          }
      });
    });
});