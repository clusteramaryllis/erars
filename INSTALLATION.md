## Installasi

Untuk development versi PHP minimum (5.4 Recommended)

- Composer => PHP 5.3.2
- Laravel => PHP 5.3.7
- Artisan => PHP 5.4

Download & install [Composer](https://getcomposer.org/download/)

Download project & extract di `namafolder` yang Anda inginkan

Dalam folder project yang sudah di extract jalankan dari command prompt

`C:\xampp\htdocs\namafolder> composer install`

`C:\xampp\htdocs\namafolder> composer dump-autoload -o`

Tunggu hingga selesai (butuh koneksi internet)

Import database terkini di folder `dump` (Jangan lupa backup database lama)

Ganti informasi di `app\config\database.php` bagian `pgsql`

```php
'pgsql' => array(
	'driver'   => 'pgsql',
	'host'     => 'localhost',
	'database' => 'namadatabase',
	'username' => 'postgres',
	'password' => '',
	'charset'  => 'utf8',
	'prefix'   => '',
	'schema'   => 'public',
	'port'     => '5432',
)
```

Setelah selesai, jalankan juga dari command prompt

`C:\xampp\htdocs\namafolder> php artisan serve`

Open browser dan ketik `localhost:8000`

Selesai. 
