## Installasi

Download & install [Composer](https://getcomposer.org/download/)

Download project & extract

Dalam folder project yang sudah di extract jalankan dari command prompt

`composer install`

`composer dump-autoload -o`

Tunggu hingga selesai (butuh koneksi internet)

Import backup database di folder `dump`

Ganti informasi di `app\config\database.php` bagian `pgsql`

Setelah selesai, jalankan juga dari command prompt

`php artisan serve`

Open browser dan ketik `localhost:8000`

Selesai. 

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
