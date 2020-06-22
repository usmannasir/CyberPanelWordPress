<?php
add_action( 'admin_enqueue_scripts', 'script_include' );

function script_include() {
	if (isset($_GET['page'])){
		if ( strpos($_GET['page'],'cyber') === false){
			return;
		}
	}else if (!isset($_GET['page'])){
		return;
	}
	if (count($_GET) == 0){
		return;
	}
	wp_register_script( "Popper", "https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" );
	wp_register_script( "bs_js", "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" );
	wp_enqueue_script( "jQuery" );
	wp_enqueue_script( "Popper" );
	wp_enqueue_script( "bs_js" );
	add_action( 'admin_footer', 'ajax_verify_connection' );
	//css
	wp_register_style( "bs_css", "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" );
	wp_enqueue_style( "bs_css" );
}
function ajax_verify_connection() {
	?>
	<script type="application/javascript">
        function verify_connection() {
            start_progress();
            var data = {
                'action': 'verify_connection',
                'cyberpanel_admin_user': jQuery('#cyberpanel_admin_user').val(),
                'cyberpanel_admin_password': jQuery('#cyberpanel_admin_password').val()
            };
            jQuery.post(ajaxurl, data, function (response) {
                if (response === "Success") {
                    showAlert("success", "Your connection has been verified successfully", false);
                }else {
                    showAlert("danger", "oops ! There was an error",false);
                }
            });
        }

        jQuery(function () {
            var hash = window.location.hash;
            hash && jQuery('ul.nav a[href="' + hash + '"]').tab('show');

            jQuery('.nav-tabs a').click(function (e) {
                jQuery(this).tab('show');
                var scrollmem = jQuery('body').scrollTop();
                window.location.hash = this.hash;
                jQuery('html,body').scrollTop(scrollmem);
            });
        });

        function delete_settings() {
            start_progress();
            var data = {
                'action': 'delete_settings'
            };
            jQuery.post(ajaxurl, data, function (response) {
                if (response == "1") {
                    showAlert("success", "Settings delete successful",true);
                } else {
                    showAlert("danger", "oops ! There was an error",true);
                }
            });
        }

        function start_progress() {
            jQuery("#progress").modal('show');
        }

        function stop_progress() {
            jQuery("#progress").modal('hide');
        }

        function showAlert(status, message , refresh) {
            stop_progress();
            jQuery("#alert").append('<div class="alert fade show alert-' + status + '" role="alert">\n' +
                message +
                '</div>');
            hide_alert(refresh);
        }

        function hide_alert(refresh) {
            window.setTimeout(function () {
                jQuery(".alert").fadeTo(500, 0).slideUp(500, function () {
                    jQuery(this).alert('close');

                    if (refresh){
                        location.reload();
                    }
                });
            }, 3000);

        }
	</script>
	<?php
}

?>