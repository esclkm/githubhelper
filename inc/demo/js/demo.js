$(document).ready(function(){

  $('#btn-request-access-code').click(function() {
    var client_id = $('#tbx-client-id').val();
    var client_secret = $('#tbx-client-secret').val();
    var url = 'index.php?op=request_access_code';
    url += '&client_id=' + client_id;
    url += '&client_secret=' + client_secret;
    window.location.href = url;
  });

  $('#btn-request-access-token').click(function() {
    window.location.href = 'index.php?op=request_access_token';
  });

  $('#btn-api-request').click(function() {
    var endpoint = $('#tbx-endpoint').val();
    var http_verb = 'GET'; // $('#drp-http-verb').val();
    window.location.href = 'index.php?op=api_request&endpoint=' + endpoint + '&http_verb=' + http_verb;
  });

  $('#btn-reset').click(function() {
    window.location.href = 'index.php?op=reset';
  });

  $(document).keydown(function() {
    if(event.keyCode == '13') {
      if($('#btn-request-access-code')[0] !== undefined) {
        $('#btn-request-access-code').click();
      } else if($('#btn-request-access-token')[0] !== undefined) {
        $('#btn-request-access-token').click();
      } else if($('#btn-api-request')[0] !== undefined) {
        $('#btn-api-request').click();
      } 
    }
  });
});