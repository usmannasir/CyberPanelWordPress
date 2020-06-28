function GlobalAjax(dataContent) {
    jQuery(document).ready(function ($) {
        $("#WPCPSpinner").show();
        $("#WPCPSpinnerModal").show();
        $.post(CPWP.ajax_url, dataContent
            , function (data) {
                $("#WPCPSpinner").hide();
                $("#WPCPSpinnerModal").hide();

                if (dataContent.action !== 'jobStatus'){

                    dataContent = {
                        _ajax_nonce: CPWP.nonce,
                        action: 'jobStatus'
                    }

                    GlobalAjax(dataContent);
                    return;
                }

                if (data.status === 1) {
                    $(document).ready(function () {
                        try {
                            $("#jobStatusResult").html(jsonData.result);
                        } catch (e) {
                        }
                        $("#jobsModal").modal('show')
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
    });
    $("#viewJobs").click(function (){

        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'jobStatus'
        }

        GlobalAjax(dataContent);

    });
    $("#viewJobsModal").click(function (){

        dataContent = {
            _ajax_nonce: CPWP.nonce,
            action: 'jobStatus'
        }

        GlobalAjax(dataContent);

    });
});

///
