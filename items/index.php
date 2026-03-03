<?php
// items/index.php
// Prevent Apache directory listing and send users to the Browse page.
require_once __DIR__ . '/../includes/config.php';
redirect('items/search.php');
