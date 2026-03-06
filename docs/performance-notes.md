# Performance Notes — Production Readiness

Dokumen ini mencatat semua tuning yang perlu dilakukan di VPS production
sebelum launch. Bukan wajib di local dev, tapi **wajib di production**.

---

## 1. Redis (WAJIB)

### Install (Ubuntu VPS)
```bash
apt update && apt install redis-server
systemctl enable redis-server
systemctl start redis-server
redis-cli ping  # Harus return: PONG
```

### Konfigurasi .env
```ini
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

### Windows Local Dev (Memurai)
Download: https://www.memurai.com/
Gratis untuk development. API-compatible dengan Redis penuh.

---

## 2. MySQL: max_connections

**Masalah:** MySQL default `max_connections = 151`.
Dengan 300 concurrent request + PHP-FPM → connection exhaustion → 500 errors.

### Kalkulasi aman:
```
max_connections = (RAM_GB * 200) - 50
```
- 2GB RAM → max ~350 connections
- 4GB RAM → max ~750 connections
- 8GB RAM → max ~1550 connections

**Jangan set lebih tinggi dari kalkulasi ini** — setiap idle connection pakai RAM.

### Edit `/etc/mysql/mysql.conf.d/mysqld.cnf`:
```ini
[mysqld]
max_connections         = 300     # Sesuaikan dengan RAM
innodb_buffer_pool_size = 512M    # 50-70% total RAM untuk DB-heavy app
slow_query_log          = 1
slow_query_log_file     = /var/log/mysql/slow-query.log
long_query_time         = 1       # Log query > 1 detik
```

```bash
systemctl restart mysql
mysql -e "SHOW VARIABLES LIKE 'max_connections';"
```

---

## 3. PHP-FPM Pool

**Masalah:** PHP-FPM pool size harus sesuai dengan MySQL `max_connections`.
Kalau pool > max_connections → connection exhaustion otomatis.

### Edit `/etc/php/8.x/fpm/pool.d/www.conf`:
```ini
pm                  = dynamic
pm.max_children     = 50      ; Jangan lebih dari max_connections/2
pm.start_servers    = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests     = 500     ; Restart worker setiap 500 req (prevent memory leak)
```

```bash
systemctl restart php8.x-fpm
```

---

## 4. PHP OPcache

OPcache menyimpan compiled PHP bytecode di memory → skip proses compile setiap request.
Di local dev biasanya sudah aktif, tapi cek settingnya.

### Edit `php.ini` (atau `/etc/php/8.x/fpm/conf.d/10-opcache.ini`):
```ini
opcache.enable            = 1
opcache.memory_consumption = 256    ; MB — cukup untuk Laravel + dependencies
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 20000
opcache.validate_timestamps   = 0   ; Di production: matikan (pakai 0)
opcache.revalidate_freq       = 0   ; 0 = paksa pakai cache (restart FPM untuk deploy baru)
opcache.fast_shutdown         = 1
```

> ⚠️ **Kalau `validate_timestamps = 0`**: Setiap deploy harus jalankan:
> ```bash
> php artisan opcache:clear   # Atau: kill -USR2 $(cat /run/php/php8.x-fpm.pid)
> ```

---

## 5. Nginx Config (Production)

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/cuan-workflow/public;

    index index.php;

    # Gzip compression
    gzip on;
    gzip_types text/plain application/json application/javascript text/css;
    gzip_min_length 1024;

    # Static files cache
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff2)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.x-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;   # Match job timeout
    }

    # Rate limiting (tambah di nginx level sebagai first line defense)
    limit_req_zone $binary_remote_addr zone=api:10m rate=60r/m;
    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

---

## 6. Queue Worker: Supervisor (Linux Production)

Lihat `supervisor.conf` di root project untuk config lengkap.

```bash
# Copy ke supervisor
cp supervisor.conf /etc/supervisor/conf.d/cuan-workflow.conf

# Edit path sesuai deployment
nano /etc/supervisor/conf.d/cuan-workflow.conf

# Aktifkan
supervisorctl reread
supervisorctl update
supervisorctl start cuan-workflow-worker:*
supervisorctl status
```

**Scale workers:**
```ini
; Di supervisor.conf — ubah satu baris ini:
numprocs=5  ; dari 3 ke 5 untuk 1000+ concurrent user
```

---

## 7. Laravel Horizon (Opsional tapi Direkomendasikan)

Kalau pakai Redis queue, Horizon memberikan monitoring real-time:
queue depth, job throughput, failed jobs, per-queue stats.

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate

# Jalankan (via Supervisor di production)
php artisan horizon
```

Dashboard: `http://yourdomain.com/horizon` (pastikan auth-protected)

---

## 8. Monitoring Minimal (Post-Launch)

Tanpa monitoring = tidak tahu kapan sistem mulai kelelahan.

### Queue monitor (sederhana):
```bash
# Cek backlog setiap 10 detik
watch -n 10 'php artisan queue:monitor redis:default'
```

### Slow query log (sudah aktif jika ikut step 2):
```bash
tail -f /var/log/mysql/slow-query.log
```

### Laravel log:
```bash
tail -f /var/www/cuan-workflow/storage/logs/laravel.log | grep -E "ERROR|CRITICAL|WARNING"
```

---

## Checklist Pre-Launch

- [ ] Redis running: `redis-cli ping`
- [ ] `.env` updated: `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`
- [ ] DB migration ran: `php artisan migrate`
- [ ] Supervisor running: `supervisorctl status`
- [ ] k6 polling flood test: p95 < 100ms ✅
- [ ] k6 heavy endpoint test: p95 < 500ms ✅
- [ ] k6 stepwise test: stable di 300 VU ✅
- [ ] OPcache enabled: `php -r "echo opcache_get_status()['opcache_enabled'];"`
- [ ] MySQL slow query log aktif
- [ ] Laravel `.env`: `APP_DEBUG=false`, `APP_ENV=production`
