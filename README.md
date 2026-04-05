<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-CDN-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" />
  <img src="https://img.shields.io/badge/PWA-Ready-5A0FC8?style=for-the-badge&logo=pwa&logoColor=white" />
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" />
</p>

<h1 align="center">🏓 Interliga Pádel CR</h1>

<p align="center">
  <strong>Sistema completo de gestión de ligas de pádel</strong><br>
  Inscripciones · Jornadas · Partidos · Clasificación · Panel Admin
</p>

<p align="center">
  <a href="#-demo">Demo</a> •
  <a href="#-features">Features</a> •
  <a href="#-instalación">Instalación</a> •
  <a href="#-deploy">Deploy</a> •
  <a href="#-stack">Stack</a>
</p>

---

## 🎯 Features

### 📱 App Pública (Mobile-First)
- **Jornadas** con countdown animado (días:horas:minutos)
- **Inscripción** como pareja, jugador solo o reserva/suplente
- **Lista de espera** automática cuando la cancha está llena
- **Clasificación** en tiempo real con tabla de posiciones
- **Dark mode** con toggle y persistencia en localStorage
- **PWA** — instalable en el celular, funciona offline
- **Glassmorphism UI** con animaciones premium

### ⚡ Panel Admin
- **Dashboard** con stats animados y acciones rápidas
- **CRUD completo** de jornadas, partidos, equipos y resultados
- **Gestión de inscritos** con edición inline, swap de reservas, promoción de espera
- **Acciones en masa** — cancelar/mover a reserva múltiples jugadores
- **Búsqueda** por nombre o teléfono en inscripciones
- **Duplicar partidos** con un click
- **Categorías predefinidas** (1ra-6ta fuerza, Principiantes, Open)
- **Exportar CSV** de inscripciones para Excel
- **Backup SQL** descargable del panel
- **Log de auditoría** paginado con historial de acciones
- **Notificación WhatsApp** — mensaje pre-armado por jugador
- **Dark mode** sincronizado

### 🔒 Seguridad
- CSRF token en todas las operaciones POST
- Rate limiting en login (5 intentos / 5 min)
- Timeout de sesión admin (30 min)
- Passwords hasheados con bcrypt
- Prepared statements (zero SQL injection)
- Validación de enums en backend

---

## 📂 Estructura

```
interliga/
├── config.php        # Configuración DB + funciones core
├── api.php           # REST API (~30 endpoints)
├── index.php         # App pública (SPA-like)
├── admin.php         # Panel de administración
├── install.php       # Instalador automático (ejecutar 1 vez)
├── setup.sql         # Esquema SQL completo + datos iniciales
├── sw.js             # Service Worker (offline/cache)
├── manifest.json     # PWA manifest
├── .gitignore
└── README.md
```

---

## 🚀 Instalación

### Requisitos
- **PHP** 8.0+
- **MySQL** 5.7+ / MariaDB 10.3+
- Servidor web (Apache/Nginx/XAMPP/Laragon)

### Setup rápido (local)

```bash
# 1. Clonar el repo
git clone https://github.com/wupckoding/interligas.git
cd interligas

# 2. Copiar a tu webroot (XAMPP ejemplo)
# Los archivos ya están listos para servir

# 3. Editar credenciales de DB
# Abrir config.php y ajustar:
#   DB_HOST, DB_NAME, DB_USER, DB_PASS

# 4. Ejecutar el instalador
# Abrir en el navegador: http://localhost/interliga/install.php
# Esto crea las tablas + usuario admin automáticamente

# 5. Listo! 🎉
# App:   http://localhost/interliga/
# Admin: http://localhost/interliga/admin.php
```

### Credenciales por defecto

| Campo       | Valor            |
|-------------|------------------|
| **Usuario** | `admin`          |
| **Clave**   | `interliga2024`  |

> ⚠️ **Cambiar la contraseña** después del primer login y **eliminar `install.php`** en producción.

---

## 🌐 Deploy (Hosting)

### Hostinger / cPanel / Shared Hosting

1. **Crear base de datos** desde el panel de hosting (MySQL Databases)
2. **Anotar** host, nombre_db, usuario y contraseña
3. **Editar `config.php`** con los datos reales:
   ```php
   define('DB_HOST', 'localhost');       // o el host del panel
   define('DB_NAME', 'u123_interliga'); // nombre real
   define('DB_USER', 'u123_admin');     // usuario real
   define('DB_PASS', 'tu_password');    // contraseña real
   ```
4. **Subir archivos** vía File Manager o FTP a `public_html/`
5. **Importar `setup.sql`** desde phpMyAdmin (o acceder a `install.php`)
6. **Eliminar `install.php`** después de la instalación
7. **Acceder** a `tusitio.com/admin.php` 

### VPS / Docker

```bash
# Con PHP built-in server (desarrollo)
php -S 0.0.0.0:8080 -t /path/to/interliga

# Con ngrok para compartir
ngrok http 8080
```

---

## 🛠 Stack Técnico

| Componente | Tecnología |
|-----------|-----------|
| **Backend** | PHP 8.x vanilla (sin frameworks) |
| **Database** | MySQL/MariaDB con PDO |
| **Frontend** | HTML5 + Vanilla JS (SPA-like) |
| **CSS** | Tailwind CSS via CDN |
| **PWA** | Service Worker + Web App Manifest |
| **Auth** | Sessions + bcrypt + CSRF tokens |
| **UI** | Glassmorphism, animaciones CSS, ripple effects |

---

## 📡 API Endpoints

<details>
<summary><strong>Ver todos los endpoints (~30)</strong></summary>

#### Público
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `?action=jornadas_list` | Listar jornadas |
| GET | `?action=partidos_list&jornada_id=X` | Listar partidos |
| GET | `?action=inscripciones_list&partido_id=X` | Listar inscritos |
| GET | `?action=espera_list&partido_id=X` | Lista de espera |
| GET | `?action=clasificacion` | Tabla de posiciones |
| POST | `?action=inscribir` | Inscribirse a partido |

#### Admin (requiere sesión)
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| POST | `?action=admin_login` | Login |
| POST | `?action=admin_logout` | Logout |
| GET | `?action=admin_check` | Verificar sesión |
| GET | `?action=stats` | Dashboard stats |
| POST | `?action=jornada_create/update/delete` | CRUD jornadas |
| POST | `?action=partido_create/update/delete` | CRUD partidos |
| POST | `?action=equipo_create/update/delete` | CRUD equipos |
| POST | `?action=resultado_create/update/delete` | CRUD resultados |
| POST | `?action=cancelar_inscripcion` | Cancelar inscripción |
| POST | `?action=promover_espera` | Promover de espera |
| POST | `?action=swap_reserva` | Intercambiar titular↔reserva |
| POST | `?action=hacer_reserva` | Toggle titular/reserva |
| POST | `?action=inscripcion_update` | Editar inscripción |
| POST | `?action=duplicar_partido` | Duplicar partido |
| POST | `?action=bulk_cancelar` | Cancelar en masa |
| POST | `?action=bulk_hacer_reserva` | Reserva en masa |
| GET | `?action=exportar_inscripciones` | Exportar CSV |
| GET | `?action=backup_db` | Descargar backup SQL |
| GET | `?action=audit_log_list` | Log de auditoría |
| GET | `?action=buscar_inscripciones` | Buscar jugadores |

</details>

---

## 📄 Licencia

MIT © [wupckoding](https://github.com/wupckoding)

---

<p align="center">
  Hecho con 💚 para la comunidad de pádel de Costa Rica 🇨🇷
</p>
