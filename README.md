# filterable-news-api
News aggregator with PHP/CodeIgniter

## Setup
1. Enable Apache mod_rewrite  
```
sudo a2enmod rewrite
```
2. Set AllowOverride and [for production system] set environment variable
```
sudo nano /etc/apache2/apache2.conf
```
Paste this:
```
<Directory /var/www/api.example.com/public_html/>
    AllowOverride All
    SetEnv CI_ENV production
</Directory>
```
3. Restart Apache
```
sudo systemctl restart apache2
```
4. Create database and grant authorization to db user
```
mysql -u root -p
CREATE DATABASE dbname DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL ON dbname.* TO 'dbuser'@'localhost' IDENTIFIED BY 'password';FLUSH PRIVILEGES;
EXIT;
```
5. Clone this repo as public_html will be document root
```
cd /var/www
sudo git clone https://github.com/kkayacan/filterable-news-api.git
sudo mv filterable-news-api api.example.com
```
6. Copy configuration files to environment directory
```
cd /var/www/api.example.com/application/config
sudo mkdir production
sudo cp config.php production/config.php
sudo cp database.php production/database.php
sudo cp rest.php production/rest.php
```
7. Set base url in config.php
```
$config['base_url'] = 'https://api.example.com/';
```
8. Set database info in database.php
```
$db['default'] = array(
        'dsn'   => '',
        'hostname' => 'localhost',
        'username' => 'dbuser',
        'password' => 'password',
        'database' => 'dbname',
```
9. [If frontend is on different origin] Enable CORS in rest.php
```
$config['check_cors'] = TRUE;
```
If frontend domain unknown
```
$config['allow_any_cors_domain'] = TRUE;
```
If frontend domain known
```
$config['allowed_cors_origins'] = ['https://example.com', 'https://www.example.com'];
```
10. Change ownership and permissions
```
cd /var/www
sudo chown -R ownername:www-data api.example.com  
cd api.example.com  
sudo find . -type f -exec chmod 664 {} +  
sudo find . -type d -exec chmod 775 {} +
```