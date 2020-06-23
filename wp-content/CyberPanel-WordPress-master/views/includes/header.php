<div class="container-fluid">

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand"
           href="<?php echo admin_url( "admin.php?page=cyberpanel-hosting-config" ); ?>">CyberPanel</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="echo admin_url( " admin.php?page=cyberpanel-hosting-config" );">
                    Home <span class="sr-only">(current)</span>
                    </a>
                </li>

            </ul>
        </div>
    </nav>
    <div id="alert" class="justify-content-center"></div>
</div>
<div class="modal fade" id="progress" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <div class="spinner-grow text-success" role="status">
                        <span class="sr-only">Processing</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>