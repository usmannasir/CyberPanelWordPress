function GlobalAjax(dataContent) {
    jQuery(document).ready(function ($) {           //wrapper
        $.post(CPWP.ajax_url, dataContent
            , function (data) {                    //callback
                var jsonData = JSON.parse(data.body);
                alert(jsonData.status);             //insert server response
                if (jsonData.status === 1) {
                    $(document).ready(function () {
                        $("#myToast").toast('show');
                    });

                }
            });
    });
}

$("#connectServer").click(function () {
    var dataContent = {
        _ajax_nonce: CPWP.nonce,
        action: 'connectServer',
        hostname: $("#hostname").val(),
        username: $("#username").val(),
        password: $("#password").val()
    }
    GlobalAjax(dataContent);
});