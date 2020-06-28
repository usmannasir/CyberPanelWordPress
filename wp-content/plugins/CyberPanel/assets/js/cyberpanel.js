function GlobalAjax(dataContent) {
    $("#WPCPSpinner").show();
    jQuery(document).ready(function ($) {           //wrapper
        $.post(CPWP.ajax_url, dataContent
            , function (data) {
                $("#WPCPSpinner").hide();
                var jsonData = data;

                console.log(jsonData);
                console.log(typeof jsonData);

                if (jsonData.status === 1) {
                    $(document).ready(function () {
                        try {
                            $("#jobStatusResult").html(jsonData.result);
                        } catch (e) {
                        }
                        $(".toast").toast('show');
                        $("#toastCustom").toast('show');
                    });
                }
            });
    });
}

jQuery(document).ready(function ($) {
    $("#WPCPSpinner").hide();
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

///
