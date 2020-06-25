jQuery(document).ready(function($) {           //wrapper
    $( "#target" ).click(function() {             //event
        var this2 = this;                      //use in callback
        $.post(CPWP.ajax_url, {         //POST request
            _ajax_nonce: CPWP.nonce,     //nonce
            action: "my_tag_count",            //action
            title: this.value                  //data
        }, function(data) {                    //callback
            this2.nextSibling.remove();        //remove current title
            $(this2).after(data);              //insert server response
        });
    });
});