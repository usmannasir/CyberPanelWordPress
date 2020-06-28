<?php include("header.php"); ?>

    <div class="container-fluid WPCPContainer">
        <div class="card-header">
            <h4 class="my-0 font-weight-normal">Configure Hetzner API Tokens</h4>
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="hostname" class="sr-only">Name</label>
                    <input type="text" id="Name" class="form-control" placeholder="Name" required>
                    <small id="Name" class="form-text text-muted">Give Name to this API Token.</small>
                </div>
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Token</label>
                    <input type="text" id="Token" class="form-control" placeholder="Token" required>
                    <small id="Token" class="form-text text-muted">Actual API Token From Hetzner</small>
                </div>
                <button id="connectHetzner" type="button" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

<?php include("footer.php"); ?>