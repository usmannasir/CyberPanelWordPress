<?php include("header.php"); ?>

<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
        <form class="form-signin">
            <h1 class="h3 mb-3 font-weight-normal">Connect Servers</h1>
            <label for="inputEmail" class="sr-only">Username</label>
            <input type="email" id="username" class="form-control" placeholder="Username" required autofocus>
            <label for="inputPassword" class="sr-only">Password</label>
            <input type="password" id="password" class="form-control" placeholder="Password" required>
            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" value="remember-me"> Remember me
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            <p class="mt-5 mb-3 text-muted">&copy; 2017-2018</p>
        </form>
    </div>

</div>
