Options +FollowSymLinks -MultiViews
# Turn mod_rewrite on
RewriteEngine On

RewriteBase /
RewriteCond %{THE_REQUEST} ^.*\/index\.php\ HTTP/
RewriteRule ^(.*)index\.php$ /$1 [R=301,L]
# To externally redirect /dir/foo.php to /dir/foo
RewriteCond %{THE_REQUEST} ^[A-Z]{3,}\s([^.]+)\.php [NC]
RewriteRule ^ %1 [R,L,NC]

## To internally redirect /dir/foo to /dir/foo.php
RewriteCond %{REQUEST_FILENAME}.php -f [NC]
RewriteRule ^ %{REQUEST_URI}.php [L]

AuthName "home"
AuthUserFile "/home/csgowhe1/.htpasswds/public_html/home/passwd"

## To redirect to https
RewriteEngine On
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST} [L,R=301]
