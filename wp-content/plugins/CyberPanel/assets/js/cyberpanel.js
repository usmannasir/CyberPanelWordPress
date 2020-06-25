jQuery(document).ready(function($) {           //wrapper
    $( "#connectServer" ).click(function() {
        $.post(CPWP.ajax_url, {         //POST request
            _ajax_nonce: CPWP.nonce,     //nonce
            action: "connectServer",            //action
            hostname: $("#hostname").val(),                  //data
            username: $("#username").val(),
            password: $("#password").val(),
        }, function(data) {                    //callback
            var jsonData = data.body;
            alert(jsonData);             //insert server response
        });
    });
});