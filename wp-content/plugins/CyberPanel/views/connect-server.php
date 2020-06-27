<?php include("header.php"); ?>

<div class="container-fluid WPCPContainer">
    <div class="card-header">
        <h4 class="my-0 font-weight-normal">Connect</h4>
    </div>
    <div class="card-body">
        <form>
            <div class="form-group">
                <label for="hostname" class="sr-only">Hostname</label>
                <input type="text" id="hostname" class="form-control" placeholder="Hostname" required>
                <small id="emailHelp" class="form-text text-muted">Hostname excluding http protocol</small>
            </div>
            <div class="form-group">
                <label for="inputEmail" class="sr-only">Username</label>
                <input type="text" id="username" class="form-control" placeholder="Username" required>
                <small id="emailHelp" class="form-text text-muted">Hostname excluding http protocol</small>
            </div>
            <div class="form-group">
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="password" class="form-control" placeholder="Password" required>
                <small id="emailHelp" class="form-text text-muted">Hostname excluding http protocol</small>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>