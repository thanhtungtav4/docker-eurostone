<?php
define( 'SMTP_username', 'nguyenthanhtung74119@gmail.com' );  // username of host like Gmail
define( 'SMTP_password', 'etqqtaaeckfgxqzq' );   // password for login into the App
define( 'SMTP_server', 'smtp.gmail.com' );     // SMTP server address
define( 'SMTP_FROM', 'nguyenthanhtung74119@gmail.com' );   // Your Business Email Address
define( 'SMTP_NAME', 'I_DENT' );   //  Business From Name
define( 'SMTP_PORT', '587' );     // Server Port Number
define( 'SMTP_SECURE', 'tls' );   // Encryption - ssl or tls
define( 'SMTP_AUTH', true );  // Use SMTP authentication (true|false)
define( 'SMTP_DEBUG',   0 );  // for debugging purposes only

add_action( 'phpmailer_init', 'my_phpmailer_smtp' );
function my_phpmailer_smtp( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host = SMTP_server;
    $phpmailer->SMTPAuth = SMTP_AUTH;
    $phpmailer->Port = SMTP_PORT;
    $phpmailer->Username = SMTP_username;
    $phpmailer->Password = SMTP_password;
    $phpmailer->SMTPSecure = SMTP_SECURE;
    $phpmailer->From = SMTP_FROM;
    $phpmailer->FromName = SMTP_NAME;
}

 
