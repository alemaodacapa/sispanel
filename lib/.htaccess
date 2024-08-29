# Definir limites de tamanho de upload e timeout
php_value post_max_size 8M
php_value upload_max_filesize 2M
php_value max_execution_time 30

# Ativar o mecanismo de reescrita
RewriteEngine On

# Reescrever apenas quando o método POST for usado
RewriteCond %{REQUEST_METHOD} POST

# Reescrever regras
RewriteRule ^registro/?$ /login.php [L]
RewriteRule ^registro/?$ /registro.php [L]
RewriteRule ^painel/?$ /painel.php [L]
RewriteRule ^relatorio/?$ /relatorio.php [L]
RewriteRule ^relatorio/?$ /validados.php [L]
RewriteRule ^relatorio/?$ /validacao_atendimento.php [L]

# Configurações do diretório (certifique-se de ajustar o caminho se necessário)
<Directory /var/www/html>
    AllowOverride All
    Require all granted
</Directory>

# Aplicar manipulação de arquivos PHP
<FilesMatch "\.php$">
    SetHandler application/x-httpd-php
</FilesMatch>
