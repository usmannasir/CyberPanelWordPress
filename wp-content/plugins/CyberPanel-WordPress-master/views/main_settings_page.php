<?php include( CyberPanel_PLUGINDIR . "/views/includes/header.php" ); ?>

    <div class="shadow-lg p-3 mt-5 mb-5 bg-white rounded">
        <ul class="shadow-sm nav nav-tabs justify-content-center" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home"
                   role="tab" aria-controls="home"
                   aria-selected="true">Enter & Verify cyberpanel credentials</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile"
                   role="tab" aria-controls="profile"
                   aria-selected="false">Delete connection settings</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="fluid-container pt-5">
                    <div class="text-center col-sm-4 offset-sm-4">
						<?php if ( is_null( $result ) ){ ?>
                        <form
                                action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
                                method="post">
                            <input type="hidden" name="action" value="cyberpanel_verify">
							<?php } ?>
                            <div class="form-group">
                                <label for="cyberpanel_hostname">Enter your hostname</label>
                                <input required name="cyberpanel_hostname" type="text"
                                       class="form-control form-control-lg" id="cyberpanel_hostname"
                                       value="<?php is_null( $result ) ?: print_r( $result[0]->setting_value ); ?>"
                                       placeholder="Enter Hostname for your cyberpanel">
                            </div>
                            <div class="form-group">
                                <label for="cyberpanel_admin_user">Enter your admin user name</label>
                                <input required name="cyberpanel_admin_user" type="text"
                                       class="form-control form-control-lg"
                                       value="<?php is_null( $result ) ?: print_r( $result[1]->setting_value ); ?>"
                                       id="cyberpanel_admin_user"
                                       placeholder="Enter admin name">
                            </div>
                            <div class="form-group">
                                <label for="cyberpanel_admin_password">Enter your password</label>
                                <input required name="cyberpanel_admin_password" type="password"
                                       class="form-control form-control-lg"
                                       value="<?php is_null( $result ) ?: print_r( $result[2]->setting_value ); ?>"
                                       id="cyberpanel_admin_password"
                                       placeholder="Enter admin password">
                            </div>
                            <button <?php is_null( $result ) ? print_r( 'type="submit"' ) : print_r( 'onclick="verify_connection()"' ); ?>
                                    class="btn btn-primary"><?php is_null( $result ) ? print_r( "Save" ) : print_r( "Verify Connection" ); ?></button>
							<?php if ( is_null( $result ) ){ ?>
                        </form>
					<?php } ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade m-5" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                <div class="fluid-container">
                    <div class="text-center col-sm-4 offset-sm-4">
                        <button onclick="delete_settings()" class="btn btn-outline-success">
                            Delete save connection settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
