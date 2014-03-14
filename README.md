## Installasi

Download & Install [Composer](https://getcomposer.org/download/)

Download Project & Extract

Dalam Folder Project yang sudah di extract jalankan dari command prompt

`composer install`

`composer dump-autoload -o`

Tunggu hingga selesai (butuh koneksi internet)

Ganti informasi di `app\config\database.php` bagian `pgsql`

Setelah selesai, jalankan juga dari command prompt

`php artisan serve`

open browser dan ketik `localhost:8000`

Selesai. 

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
