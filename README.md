# 📚 Library Management System

Sistem Manajemen Perpustakaan berbasis **PHP Native** dengan antarmuka modern menggunakan **Tailwind CSS**.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-CDN-06B6D4?logo=tailwindcss&logoColor=white)

---

## ✨ Fitur

- 🔐 **Sistem Autentikasi** - Login dengan session management
- 📊 **Dashboard** - Ringkasan statistik perpustakaan
- 📖 **Manajemen Buku** - CRUD data buku
- 👥 **Manajemen Anggota** - CRUD data anggota perpustakaan
- 📝 **Transaksi Peminjaman** - Kelola peminjaman dan pengembalian buku
- 📱 **Responsive Design** - Tampilan optimal di desktop dan mobile

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP Native (PDO) |
| Database | MySQL |
| Frontend | HTML, Tailwind CSS (CDN), JavaScript |
| Font | Inter (Google Fonts) |

---

## 📁 Struktur Direktori

```
library_native/
├── assets/
│   ├── css/          # Stylesheet tambahan
│   ├── images/       # Gambar dan aset media
│   └── js/           # JavaScript files
├── config/
│   └── database.php  # Konfigurasi koneksi database
├── includes/
│   ├── header.php    # Header template dengan navigasi
│   ├── footer.php    # Footer template
│   └── session.php   # Session management
├── pages/
│   ├── admin/
│   │   ├── dashboard.php      # Halaman dashboard
│   │   ├── books.php          # Manajemen buku
│   │   ├── members.php        # Manajemen anggota
│   │   └── transactions.php   # Manajemen transaksi
│   └── auth/
│       ├── login.php          # Halaman login
│       └── logout.php         # Proses logout
├── index.php         # Entry point (redirect ke login)
└── README.md         # Dokumentasi project
```

---

## 🗄️ Struktur Database

Buat database dengan nama `library_native` dan jalankan SQL berikut:

```sql
-- Tabel Users (Admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Books
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publisher VARCHAR(100),
    year INT,
    isbn VARCHAR(20),
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Members
CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Transactions
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    member_id INT NOT NULL,
    borrow_date DATE NOT NULL,
    return_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Insert Default Admin User (password: admin123)
INSERT INTO users (username, password, name, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');
```

---

## ⚙️ Instalasi

### Prasyarat

- PHP 8.x
- MySQL 8.x
- Web Server (Apache/Nginx/Laragon/XAMPP/Herd)

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   git clone <repository-url> library_native
   cd library_native
   ```

2. **Setup Database**
   - Buat database baru dengan nama `library_native`
   - Import SQL di atas atau jalankan query secara manual

3. **Konfigurasi Database**
   
   Edit file `config/database.php` sesuai dengan pengaturan server Anda:
   ```php
   $host = '127.0.0.1';
   $db   = 'library_native';
   $user = 'root';
   $pass = '';
   $port = '3306';  // Sesuaikan dengan port MySQL Anda
   ```

4. **Jalankan Web Server**

   **Menggunakan Laravel Herd:**
   - Link folder project ke Herd
   - Akses via `http://library_native.test`

   **Menggunakan PHP Built-in Server:**
   ```bash
   php -S localhost:8000
   ```
   Akses via `http://localhost:8000`

   **Menggunakan XAMPP/Laragon:**
   - Letakkan folder di `htdocs` (XAMPP) atau `www` (Laragon)
   - Akses via `http://localhost/library_native`

---

## 🔐 Akun Demo

| Username | Password |
|----------|----------|
| admin | admin123 |

---

## 📸 Screenshot

### Login Page
Halaman login dengan desain modern glassmorphism dan animasi floating icon.

![image_alt](https://github.com/raydiansyah/library_native/blob/a361d56f039c411e6600a763d2c36fc80e51ca48/Screenshot%202026-01-15%20at%2014.21.59.png)

### Dashboard
Menampilkan statistik:
- Total Buku
- Total Anggota
- Buku Sedang Dipinjam
- Total Transaksi
  ![image_link](https://github.com/raydiansyah/library_native/blob/a361d56f039c411e6600a763d2c36fc80e51ca48/Screenshot%202026-01-15%20at%2014.24.27.png)


---

## 🔧 Kustomisasi

### Mengubah Warna Tema

Edit konfigurasi Tailwind di `includes/header.php`:
```javascript
tailwind.config = {
    theme: {
        extend: {
            colors: {
                primary: {
                    // Kustomisasi warna di sini
                }
            }
        }
    }
}
```

### Menambah Halaman Baru

1. Buat file PHP baru di folder `pages/admin/`
2. Include header dan footer:
   ```php
   <?php
   $pageTitle = 'Nama Halaman';
   require_once __DIR__ . '/../../includes/header.php';
   ?>

   <!-- Konten halaman -->

   <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
   ```
3. Tambahkan link navigasi di `includes/header.php`

---

## 📝 Lisensi

Project ini dibuat untuk keperluan pembelajaran.

---

## 👨‍💻 Kontributor

- **Developer** - Ray Diansyah

---

<p align="center">
  Made with ❤️ using PHP Native & Tailwind CSS
</p>
