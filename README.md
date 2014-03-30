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

### Setting Virtual Host 

#### Xampp

##### 1. Buat Virtual Host

Buka directory xampp contoh `C:\xampp`

Buka dengan text editor (seperti Notepad dkk.) file `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Tambahkan kode berikut di bagian akhir file tersebut

```ini
<VirtualHost erars.io:80>
	ServerName erars.io
	DocumentRoot "C:/xampp/htdocs/erars/public"
</VirtualHost>
```

Catatan :
`erars.io` bisa diganti dengan nama domain sesuka Anda, contoh : `tofa.com` 
`"C:/xampp/htdocs/erars/public"` direktori public aplikasi erars ditaruh

Save.

##### 2. Aliaskan dengan 127.0.0.1

Buka dengan text editor (seperti Notepad dkk.) file `C:\Windows\System32\Drivers\etc\hosts`. 

Tambahkan kode berikut di bagian akhir file tersebut

127.0.0.1 erars.io

Catatan :
`erars.io` bisa diganti dengan nama domain pada saat setting yang dipakai di virtual host

Save.

!!! Rekomendasi !!!

Matikan Antivirus terlebih dahulu (Biasanya diblok).
Jalankan notepad sebagai `Run as Administrator`. dan open `C:\Windows\System32\Drivers\etc\hosts` dari notepad.

##### 3. Restart Apache

Restart Apache lewat Xampp Control Panel

Buka browser dan ketikan url `erars.io`

Selesai.

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
