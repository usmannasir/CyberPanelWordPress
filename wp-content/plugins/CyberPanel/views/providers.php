<?php include("header.php"); ?>

    <div class="container-fluid WPCPContainer">
        <div class="card-header">
            <h4 class="my-0 font-weight-normal">Configure Cloud Providers</h4>
        </div>
        <div class="card-body">
            <form>
                <div class="form-group">
                    <label for="exampleFormControlSelect1">Select Cloud Company</label>
                    <select class="form-control" id="provider">
                        <option>Hetzner</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="hostname" class="sr-only">Name</label>
                    <input type="text" id="name" class="form-control" placeholder="Name" required>
                    <small id="Name" class="form-text text-muted">Give Name to this API.</small>
                </div>
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Token</label>
                    <input type="text" id="token" class="form-control" placeholder="Token" required>
                    <small id="Token" class="form-text text-muted">Actual API Token From Hetzner</small>
                </div>
                <button id="connectProvider" type="button" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

<?php include("footer.php"); ?>