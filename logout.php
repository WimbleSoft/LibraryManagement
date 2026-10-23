<?php
require __DIR__ . '/includes/bootstrap.php';

// Signing out changes state, so it only happens through a CSRF-protected POST.
if (is_post()) {
    verify_csrf();
    sign_out();
}

redirect('login.php');
