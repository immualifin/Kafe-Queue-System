# 🌐 IP Configuration Script - Warung Kafe Project

## 📋 Overview
Script otomatis untuk mengkonfigurasi IP server dan client pada project Warung Kafe dengan mudah dan aman.

## 🚀 Cara Penggunaan

### 1. Mode Interaktif (Recommended)
```bash
./configure.sh
# atau
./configure.sh --interactive
```
Script akan meminta input IP server dan client secara interaktif.

### 2. Mode Langsung
```bash
./configure.sh <SERVER_IP> <CLIENT_IP>
# Contoh:
./configure.sh 192.168.1.100 192.168.1.50
```

### 3. Mode Lihat Konfigurasi
```bash
./configure.sh --show
# atau
./configure.sh -s
```

### 4. Mode Test Koneksi
```bash
./configure.sh --test <SERVER_IP>
# Contoh:
./configure.sh --test 192.168.1.100
```

### 5. Mode Help
```bash
./configure.sh --help
# atau
./configure.sh -h
```

## 🔧 Yang Dikerjakan Script

### Client Side Configuration
- **File**: `tugas_kafe_client.1.0/config.js`
- **Update**: `SERVER_URL` ke IP server yang ditentukan

### Server Side Configuration
- **File**: `tugas_kafe_server.1.0/config/database.php`
  - Update CORS origins untuk mengizinkan akses dari client IP
- **Files**:
  - `tugas_kafe_server.1.0/api/menus/read.php`
  - `tugas_kafe_server.1.0/api/menus/create.php`
  - `tugas_kafe_server.1.0/api/menus/update.php`
  - Update image URLs dari `localhost` ke IP server

## 📁 Files yang Dimodifikasi
1. `tugas_kafe_client.1.0/config.js` (line 4)
2. `tugas_kafe_server.1.0/config/database.php` (lines 12-14)
3. `tugas_kafe_server.1.0/api/menus/read.php` (line 40)
4. `tugas_kafe_server.1.0/api/menus/create.php` (line 92)
5. `tugas_kafe_server.1.0/api/menus/update.php` (line 167)

## 🛡️ Safety Features

### ✅ Backup Otomatis
Script akan membuat backup dari setiap file yang dimodifikasi dengan format:
```
filename.backup.YYYYMMDD_HHMMSS
```

### ✅ Validasi IP
Script akan memvalidasi format IP address sebelum konfigurasi:
```bash
✅ Valid: 192.168.1.100
✅ Valid: 10.90.35.161
❌ Invalid: 192.168.1.999
❌ Invalid: not-an-ip
```

### ✅ Detection Current IP
Script akan mendeteksi IP current komputer sebagai default value.

### ✅ Connection Testing
Script akan melakukan test koneksi ke server setelah konfigurasi.

## 📊 Contoh Output

```
============================================
    WARUNG KAFE IP CONFIGURATION v1.0
============================================

ℹ️  Current IP: 192.168.1.100
Enter Server IP [192.168.1.100]: 192.168.1.100
Enter Client IP [192.168.1.100]: 192.168.1.50

ℹ️  Configuration Summary:
   Server IP: 192.168.1.100
   Client IP: 192.168.1.50

Do you want to continue? (y/N): y

ℹ️  Updating client configuration...
✅ Backup created: tugas_kafe_client.1.0/config.js.backup.20241205_143000
✅ Updated tugas_kafe_client.1.0/config.js

ℹ️  Updating server configuration...
✅ Backup created: tugas_kafe_server.1.0/config/database.php.backup.20241205_143001
✅ Added 192.168.1.50 to CORS origins in tugas_kafe_server.1.0/config/database.php
✅ Updated image URLs in tugas_kafe_server.1.0/api/menus/read.php
✅ Updated image URLs in tugas_kafe_server.1.0/api/menus/create.php
✅ Updated image URLs in tugas_kafe_server.1.0/api/menus/update.php

ℹ️  Testing connection to server...
✅ Server 192.168.1.100 is reachable
✅ API endpoint is accessible

✅ Configuration completed successfully!

ℹ️  Current Configuration:

📱 Client Configuration:
   SERVER_URL: "http://192.168.1.100/tugas_kafe_server"

🖥️  Server CORS Configuration:
   $allowed_origins = [
       "http://localhost",
       "http://127.0.0.1",
       "http://192.168.1.37",
       "http://192.168.1.37/tugas_kafe_client"
       "http://192.168.1.50",
       "http://192.168.1.50/tugas_kafe_client",
   ];
```

## ⚡ Quick Start Guide

### Step 1: Buka Terminal
```bash
cd /opt/lampp/htdocs/uas
```

### Step 2: Jalankan Script
```bash
./configure.sh
```

### Step 3: Follow Prompts
- Masukkan IP server
- Masukkan IP client
- Konfirmasi konfigurasi

### Step 4: Test Application
- Buka client di browser
- Test login dan API calls

## 🔍 Troubleshooting

### ❌ Script Not Found
```bash
chmod +x configure.sh
```

### ❌ Permission Denied
```bash
sudo ./configure.sh
```

### ❌ Connection Failed
- Pastikan server Apache/Nginx running
- Check firewall settings
- Verify IP addresses

### ❌ Restore Backup
```bash
# Find backup files
ls -la *.backup.*

# Restore specific file
cp filename.backup.YYYYMMDD_HHMMSS filename
```

## 📞 Support

Jika ada masalah dengan script:
1. Check current configuration: `./configure.sh --show`
2. Test connection: `./configure.sh --test <IP>`
3. Restore dari backup jika perlu
4. Run script lagi dengan IP yang benar