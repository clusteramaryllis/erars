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

`127.0.0.1 erars.io`

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
