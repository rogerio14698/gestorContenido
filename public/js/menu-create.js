console.log('🚀 MENU CREATE JS CARGADO');

window.addEventListener('load', function() {
    console.log('🌟 PÁGINA COMPLETAMENTE CARGADA');
    
    setTimeout(function() {
        console.log('🔍 VERIFICANDO ELEMENTOS...');
        
        // Buscar de diferentes maneras
        console.log('--- BÚSQUEDA POR ID ---');
        const tipoEnlace = document.getElementById('tipo_enlace');
        const tipoContenido = document.getElementById('tipo_contenido_id');  
        const contentId = document.getElementById('content_id');
        
        console.log('getElementById tipo_enlace:', tipoEnlace ? '✅ EXISTE' : '❌ NO EXISTE');
        console.log('getElementById tipo_contenido_id:', tipoContenido ? '✅ EXISTE' : '❌ NO EXISTE');
        console.log('getElementById content_id:', contentId ? '✅ EXISTE' : '❌ NO EXISTE');
        
        // Buscar por atributo name
        console.log('--- BÚSQUEDA POR NAME ---');
        const byNameTipo = document.querySelector('[name="tipo_enlace"]');
        const byNameContenido = document.querySelector('[name="tipo_contenido_id"]');
        const byNameContent = document.querySelector('[name="content_id"]');
        
        console.log('querySelector name tipo_enlace:', byNameTipo ? '✅ EXISTE' : '❌ NO EXISTE');
        console.log('querySelector name tipo_contenido_id:', byNameContenido ? '✅ EXISTE' : '❌ NO EXISTE');
        console.log('querySelector name content_id:', byNameContent ? '✅ EXISTE' : '❌ NO EXISTE');
        
        // Buscar todos los select
        console.log('--- TODOS LOS SELECTS ---');
        const allSelects = document.querySelectorAll('select');
        console.log('Total de selects encontrados:', allSelects.length);
        allSelects.forEach((select, index) => {
            console.log(`Select ${index + 1}:`, {
                id: select.id || 'SIN ID',
                name: select.name || 'SIN NAME',
                class: select.className || 'SIN CLASS'
            });
        });
        
        // Verificar si hay errores en el HTML
        console.log('--- INFO DEL DOCUMENTO ---');
        console.log('DOCTYPE:', document.doctype ? document.doctype.name : 'NO DOCTYPE');
        console.log('Title:', document.title);
        console.log('URL:', window.location.href);
        
        // Intentar encontrar elementos con diferentes selectores
        const elementos = {
            tipoEnlace: tipoEnlace || byNameTipo,
            tipoContenido: tipoContenido || byNameContenido,
            contentId: contentId || byNameContent
        };
        
        if (!elementos.tipoEnlace || !elementos.tipoContenido || !elementos.contentId) {
            console.error('❌ ELEMENTOS FALTANTES - No se puede continuar');
            console.log('Elementos encontrados:');
            console.log('- tipoEnlace:', elementos.tipoEnlace ? 'OK' : 'FALTA');
            console.log('- tipoContenido:', elementos.tipoContenido ? 'OK' : 'FALTA');  
            console.log('- contentId:', elementos.contentId ? 'OK' : 'FALTA');
            return;
        }
        
        console.log('✅ TODOS LOS ELEMENTOS ENCONTRADOS - CONFIGURANDO EVENTOS...');
        
        // El resto del código de eventos aquí...
        configurarEventos(elementos);
        
    }, 500); // Aumenté a 500ms para dar más tiempo
});

function configurarEventos(elementos) {
    console.log('⚙️ Configurando eventos...');
    
    // Resto del código de eventos...
    elementos.tipoEnlace.addEventListener('change', function() {
        const tipo = this.value;
        console.log('📝 Tipo enlace cambiado a:', tipo);
        
        document.querySelectorAll('.content-config').forEach(el => el.style.display = 'none');
        
        if (tipo === 'contenido') {
            const config = document.getElementById('contenido-config');
            if (config) {
                config.style.display = 'block';
                console.log('✅ Config contenido mostrado');
            }
        } else if (tipo === 'url_externa') {
            const config = document.getElementById('url-config');
            if (config) {
                config.style.display = 'block';
                console.log('✅ Config URL mostrado');
            }
        }
    });
    
    elementos.tipoContenido.addEventListener('change', function() {
        const tipoId = this.value;
        console.log('🔍 Tipo contenido seleccionado:', tipoId);
        
        if (!tipoId) {
            elementos.contentId.innerHTML = '<option value="">Seleccione primero un tipo</option>';
            return;
        }
        
        elementos.contentId.innerHTML = '<option value="">🔄 Cargando...</option>';
        console.log('⏳ Cargando opciones desde servidor...');
        
        // AJAX real al servidor
        fetch('/admin/menus-contents-by-type?tipo_contenido_id=' + tipoId, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error: ' + response.status);
            }
            return response.json();
        })
        .then(contenidos => {
            console.log('📊 Contenidos del servidor:', contenidos.length);
            
            elementos.contentId.innerHTML = '<option value="">Seleccione un contenido</option>';
            
            if (contenidos.length > 0) {
                contenidos.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.titulo;
                    elementos.contentId.appendChild(option);
                    console.log('➕ Agregado del servidor:', item.titulo);
                });
                console.log('🎉 ¡DROPDOWN POBLADO CON DATOS REALES!');
            } else {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Sin contenidos disponibles';
                elementos.contentId.appendChild(option);
                console.log('⚠️ Sin contenidos en servidor');
            }
        })
        .catch(error => {
            console.error('❌ Error en AJAX:', error);
            elementos.contentId.innerHTML = '<option value="">Error al cargar</option>';
        });
    });
    
    console.log('🏁 Inicializando...');
    elementos.tipoEnlace.dispatchEvent(new Event('change'));
    console.log('✅ SISTEMA LISTO');
}