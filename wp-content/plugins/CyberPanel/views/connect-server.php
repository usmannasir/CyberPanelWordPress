<?php include("header.php"); ?>


<div class="pricing-header px-3 py-3 pt-md-5 pb-md-4 mx-auto text-center">
    <h1 class="display-4">Connect</h1>
    <p class="lead">Provide your CyberPanel login details to connect your servers.</p>
</div>

<div class="container">
    <div class="card-deck mb-3 text-center">
        <div class="card mb-4 shadow-sm">
        </div>
        <div class="card mb-7 shadow-sm">
            <div class="card-header">
                <h4 class="my-0 font-weight-normal">Connect</h4>
            </div>
            <div class="card-body">
                <form class="form-signin">
                    <h1 class="h3 mb-3 font-weight-normal">Connect Servers</h1>
                    <label for="hostname" class="sr-only">Hostname</label>
                    <input type="text" id="hostname" class="form-control" placeholder="Hostname" required autofocus>
                    <label for="inputEmail" class="sr-only">Username</label>
                    <input type="text" id="username" class="form-control" placeholder="Username" required>
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input type="password" id="password" class="form-control" placeholder="Password" required>
                    <button id="connectServer" class="btn btn-lg btn-primary btn-block" type="button">Connect</button>
                </form>
            </div>
        </div>
    </div>
</div>