# Gestión del Servidor Laravel - Nuntris Teatro

## 🚀 Opciones para Mantener el Servidor Activo

### ✅ **Opción 1: Screen Session (RECOMENDADO para desarrollo)**

```bash
# Iniciar servidor en screen
./server.sh screen

# Ver estado
./server.sh status

# Conectar a la sesión (para ver logs)
./server.sh connect-screen

# Detener servidor
./server.sh stop-screen
```

**Ventajas:**
- ✅ Fácil de usar
- ✅ Se mantiene activo aunque cierres la terminal
- ✅ Puedes ver los logs en tiempo real
- ✅ Fácil de detener/reiniciar

### ⚙️ **Opción 2: Servicio Systemd (para producción)**

```bash
# Instalar el servicio
./server.sh systemd-install

# Iniciar servicio
./server.sh systemd-start

# Ver estado
./server.sh systemd-status

# Detener servicio
./server.sh systemd-stop
```

**Ventajas:**
- ✅ Se inicia automáticamente al arrancar el sistema
- ✅ Se reinicia automáticamente si falla
- ✅ Gestión profesional de logs
- ✅ Ideal para servidores de producción

## 📋 **Comandos Útiles**

### Estado del servidor:
```bash
./server.sh status
```

### Ver todas las sesiones screen:
```bash
./server.sh list-screens
```

### Conectar a screen para ver logs:
```bash
./server.sh connect-screen
# (Presiona Ctrl+A, luego D para desconectar sin cerrar)
```

## 🌐 **URLs del Proyecto**

- **Sitio público**: http://localhost:8081
- **Panel admin**: http://localhost:8081/admin
- **Login**: admin@admin.com / admin123

## 🔧 **Archivos de Configuración**

- `server.sh` - Script de gestión del servidor
- `laravel-server.service` - Configuración del servicio systemd
- `.env` - Variables de entorno de Laravel

## ⚠️ **Notas Importantes**

1. **Screen** es perfecto para desarrollo y pruebas
2. **Systemd** es mejor para servidores de producción
3. El servidor se reinicia automáticamente en caso de error
4. Los logs se guardan en el journal del sistema (systemd) o en screen

## 🔍 **Solución de Problemas**

Si el servidor no arranca:
1. Verifica que el puerto 8081 esté libre: `netstat -tlnp | grep 8081`
2. Revisa los logs: `./server.sh connect-screen` o `sudo journalctl -u laravel-server`
3. Verifica permisos: `ls -la server.sh`