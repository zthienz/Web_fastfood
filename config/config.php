<?php

// Cấu hình chung
define('SITE_NAME', 'FastFood');
define('BASE_URL', 'http://localhost/Web_fastfood/');

// Cấu hình Google OAuth
define('GOOGLE_CLIENT_ID', '297138230092-rlmkuhkj2ih6v0r6t2er386k0dj1n7tb.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('GOOGLE_REDIRECT_URI', BASE_URL . 'google-callback.php');

// Cấu hình upload
define('UPLOAD_DIR', 'public/images/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');
