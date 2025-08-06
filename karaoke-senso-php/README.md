# Karaoke Sensō - PHP Migration

## 🎤 Descripción
Migración completa del sistema Karaoke Sensō de React/FastAPI/MongoDB a PHP 8.2 + MySQL 5.7 + HTML/Bootstrap.

## 📋 Requisitos del Sistema
- **XAMPP** con Apache y MySQL
- **PHP 8.2** o superior
- **MySQL 5.7** o superior
- **Navegador web** moderno

## 🚀 Instalación en XAMPP

### Paso 1: Preparar XAMPP
1. Descargar e instalar XAMPP desde https://www.apachefriends.org/
2. Iniciar Apache y MySQL desde el panel de control de XAMPP

### Paso 2: Configurar la Base de Datos
1. Abrir phpMyAdmin (http://localhost/phpmyadmin)
2. Ejecutar el script SQL ubicado en `sql/schema.sql`
3. Verificar que la base de datos `karaoke_senso` se creó correctamente

### Paso 3: Instalar el Proyecto
1. Copiar toda la carpeta `karaoke-senso-php` a `/xampp/htdocs/`
2. Asegurar que la estructura sea: `/xampp/htdocs/karaoke-senso/`

### Paso 4: Configurar Permisos
```bash
# En Windows (ejecutar como administrador)
icacls "C:\xampp\htdocs\karaoke-senso" /grant Everyone:F /T

# En macOS/Linux  
chmod -R 755 /Applications/XAMPP/htdocs/karaoke-senso
```

### Paso 5: Verificar Instalación
1. Acceder a: http://localhost/karaoke-senso
2. Verificar que la landing page carga correctamente
3. Probar el acceso administrativo: http://localhost/karaoke-senso/admin

## 🔐 Credenciales por Defecto
- **Usuario Admin:** admin@karaokesenso.com
- **Contraseña:** Senso2025*

## 📁 Estructura del Proyecto

```
karaoke-senso-php/
├── api/                    # Endpoints de API
│   ├── auth.php           # Autenticación
│   ├── events.php         # Gestión de eventos
│   ├── registrations.php  # Registros de participantes
│   ├── brands.php         # Patrocinadores
│   └── statistics.php     # Estadísticas en tiempo real
├── admin/                 # Panel administrativo
│   ├── index.php          # Dashboard principal
│   └── login.php          # Página de login
├── assets/                # Recursos estáticos
│   ├── css/
│   │   └── style.css      # Estilos personalizados
│   └── js/
│       ├── api.js         # Cliente API JavaScript
│       ├── landing.js     # Funcionalidades landing
│       └── admin.js       # Panel administrativo
├── config/                # Configuraciones
│   ├── database.php       # Conexión a MySQL
│   └── auth.php           # Sistema de autenticación
├── sql/                   # Scripts de base de datos
│   └── schema.sql         # Esquema inicial
├── index.php              # Landing page principal
├── .htaccess             # Configuración Apache
└── README.md             # Este archivo
```

## 🛠️ Funcionalidades

### Landing Page (index.php)
- ✅ Sección Hero con logo (preparado para logo oficial)
- ✅ Estadísticas en tiempo real
- ✅ Formulario de registro de participantes
- ✅ Estructura del certamen (fases, criterios, premios)
- ✅ Bases del certamen
- ✅ Sección de fechas/eventos
- ✅ Carrusel de patrocinadores
- ✅ Manifiesto emocional
- ✅ Botón flotante de registro
- ✅ Footer completo

### Panel Administrativo (admin/)
- ✅ Login seguro con sesiones
- ✅ Dashboard con tabs
- ✅ Gestión de registros de participantes
- ✅ Control de estados de pago
- ✅ Creación y administración de eventos
- ✅ Gestión de patrocinadores
- ✅ Interfaz responsive

### API Backend (api/)
- ✅ RESTful API completa
- ✅ Autenticación con sesiones PHP
- ✅ CORS habilitado
- ✅ Validación de datos
- ✅ Manejo de errores
- ✅ Respuestas JSON

## 🔧 Configuración Adicional

### Base de Datos
El archivo `config/database.php` está configurado para XAMPP por defecto:
- **Host:** localhost
- **Usuario:** root  
- **Contraseña:** (vacía)
- **Base de datos:** karaoke_senso

### Modificar Configuración
Para cambiar la configuración de base de datos, edita `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'karaoke_senso';  
private $username = 'root';
private $password = '';  // Cambiar si tienes contraseña
```

## 🧪 Pruebas

### Probar la Landing Page
1. Navegar a http://localhost/karaoke-senso
2. Verificar que las estadísticas se cargan
3. Probar el formulario de registro
4. Verificar la navegación entre secciones

### Probar Panel Admin
1. Acceder a http://localhost/karaoke-senso/admin
2. Login con credenciales por defecto
3. Probar creación de eventos
4. Verificar gestión de registros
5. Probar creación de patrocinadores

### Probar APIs
```bash
# Estadísticas
curl http://localhost/karaoke-senso/api/statistics

# Eventos
curl http://localhost/karaoke-senso/api/events

# Patrocinadores  
curl http://localhost/karaoke-senso/api/brands
```

## 🔄 Migración de Datos

### Desde la Versión React
Si tienes datos de la versión anterior en MongoDB, puedes exportarlos y crear scripts SQL para importarlos a MySQL.

### Estructura de Datos
- **users:** Usuarios administrativos
- **events:** Eventos/fechas del certamen  
- **registrations:** Inscripciones de participantes
- **brands:** Logos de patrocinadores

## 🐛 Solución de Problemas

### Error de Conexión a Base de Datos
1. Verificar que MySQL esté ejecutándose en XAMPP
2. Comprobar credenciales en `config/database.php`
3. Asegurar que la base de datos `karaoke_senso` existe

### Errores de API
1. Verificar que mod_rewrite esté habilitado en Apache
2. Comprobar que el archivo `.htaccess` está presente
3. Revisar logs de error de Apache

### Problemas de Sesión/Login
1. Verificar que las sesiones PHP estén habilitadas
2. Comprobar permisos de escritura en directorio temporal
3. Limpiar cookies del navegador

## 📞 Soporte
Para soporte técnico o consultas sobre la migración, contactar al equipo de desarrollo.

## 📄 Licencia
© 2025 Karaoke Sensō. Todos los derechos reservados.