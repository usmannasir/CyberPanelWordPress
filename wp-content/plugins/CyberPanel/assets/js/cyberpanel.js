function GlobalAjax(dataContent) {
    jQuery(document).ready(function ($) {           //wrapper
        $.post(CPWP.ajax_url, dataContent
            , function (data) {
                var jsonData = data;

                if (jsonData.status === 1) {
                    $(document).ready(function () {
                        try {
                            $.toast({
                                title: 'Current Jobs',
                                subtitle: '11 mins ago',
                                content: jsonData.result,
                                type: 'info',
                                position: 'bottom-right',
                                delay: 5000
                            });
                        } catch (e) {
                        }


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