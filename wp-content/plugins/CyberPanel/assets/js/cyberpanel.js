function GlobalAjax(dataContent) {
    jQuery(document).ready(function ($) {           //wrapper
        $.post(CPWP.ajax_url, dataContent
            , function (data) {
                var jsonData = data;

                if (jsonData.status === 1) {
                    $(document).ready(function () {
                        try {$("#jobStatusResult").html(jsonData.result);}catch (e) {}
                        $(".toast").toast('show');

                    });
                }
            });
    });
}

jQuery(document).ready(function ($) {
    $("#connectServer").click(function () {
        var dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'connectServer',
            hostname: $("#hostname").val(),
            username: $("#username").val(),
            password: $("#password").val()
        }
        GlobalAjax(dataContent);

        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'jobStatus'
        }

        GlobalAjax(dataContent);
    });
});