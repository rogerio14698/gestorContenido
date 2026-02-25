#!/bin/bash

# Script para gestionar el servidor Laravel de Nuntris Teatro
# Uso: ./server.sh [start|stop|restart|status|screen]

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$SCRIPT_DIR"
SERVICE_FILE="laravel-server.service"
SCREEN_SESSION="laravel-server"

case "$1" in
    "screen")
        echo "🚀 Iniciando servidor Laravel en Screen..."
        screen -S $SCREEN_SESSION -dm bash -c "cd $PROJECT_DIR && php artisan serve --host=0.0.0.0 --port=8081"
        echo "✅ Servidor iniciado en screen session: $SCREEN_SESSION"
        echo "📋 Para ver: screen -r $SCREEN_SESSION"
        echo "🌐 URL: http://localhost:8081"
        ;;
    "systemd-install")
        echo "📦 Instalando servicio systemd..."
        sudo cp $SERVICE_FILE /etc/systemd/system/
        sudo systemctl daemon-reload
        sudo systemctl enable laravel-server
        echo "✅ Servicio systemd instalado y habilitado"
        ;;
    "systemd-start")
        echo "🚀 Iniciando servicio systemd..."
        sudo systemctl start laravel-server
        echo "✅ Servicio iniciado"
        ;;
    "systemd-stop")
        echo "⏹️ Deteniendo servicio systemd..."
        sudo systemctl stop laravel-server
        echo "✅ Servicio detenido"
        ;;
    "systemd-status")
        echo "📊 Estado del servicio systemd:"
        sudo systemctl status laravel-server
        ;;
    "stop-screen")
        echo "⏹️ Deteniendo servidor en screen..."
        screen -S $SCREEN_SESSION -X quit 2>/dev/null || echo "No hay sesión screen activa"
        echo "✅ Screen detenido"
        ;;
    "status")
        echo "📊 Estado del servidor Laravel:"
        echo ""
        echo "🔍 Verificando screen sessions:"
        screen -ls | grep $SCREEN_SESSION || echo "No hay sesión screen de Laravel"
        echo ""
        echo "🔍 Verificando servicio systemd:"
        systemctl is-active laravel-server 2>/dev/null || echo "Servicio systemd no está activo"
        echo ""
        echo "🌐 Verificando conectividad:"
        if curl -s http://localhost:8081 >/dev/null; then
            echo "✅ Servidor Laravel está funcionando en http://localhost:8081"
        else
            echo "❌ Servidor Laravel no está respondiendo"
        fi
        ;;
    "list-screens")
        echo "📋 Sesiones de screen activas:"
        screen -ls
        ;;
    "connect-screen")
        echo "🔌 Conectando a la sesión screen del servidor..."
        screen -r $SCREEN_SESSION
        ;;
    *)
        echo "🎭 Servidor Laravel - Nuntris Teatro"
        echo ""
        echo "Uso: $0 [comando]"
        echo ""
        echo "Comandos disponibles:"
        echo "  screen              - Iniciar servidor en screen (recomendado)"
        echo "  stop-screen         - Detener servidor en screen"
        echo "  connect-screen      - Conectar a la sesión screen"
        echo "  list-screens        - Listar sesiones screen"
        echo ""
        echo "  systemd-install     - Instalar como servicio systemd"
        echo "  systemd-start       - Iniciar servicio systemd"
        echo "  systemd-stop        - Detener servicio systemd"
        echo "  systemd-status      - Estado del servicio systemd"
        echo ""
        echo "  status              - Ver estado general del servidor"
        echo ""
        echo "💡 Para desarrollo, usa: $0 screen"
        echo "💡 Para producción, usa: $0 systemd-install && $0 systemd-start"
        ;;
esac