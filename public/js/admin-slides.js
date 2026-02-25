/**
 * ADMIN SLIDES - FUNCIONALIDAD DE GESTIÓN DE SLIDES
 * 
 * Sistema completo de drag-and-drop para reordenamiento de slides
 * con funcionalidades de eliminación y gestión de orden.
 * 
 * @author Sistema de Gestión nuntristeatro
 * @version 1.0
 */

$(document).ready(function() {
    console.log('🎬 Inicializando sistema de gestión de slides...');
    
    // ==========================================
    // VARIABLES GLOBALES
    // ==========================================
    
    let isDragging = false;
    let draggedElement = null;
    let originalOrder = [];

    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    
    initializeDragAndDrop();
    initializeDeleteHandlers();
    
    // ==========================================
    // DRAG AND DROP
    // ==========================================
    
    /**
     * Inicializa el sistema de drag and drop para slides
     */
    function initializeDragAndDrop() {
        const sortableElement = document.getElementById('sortable-slides');
        
        if (!sortableElement) {
            console.warn('⚠️ Elemento sortable-slides no encontrado');
            return;
        }

        console.log('🎯 Configurando drag-and-drop para slides...');

        const sortable = Sortable.create(sortableElement, {
            handle: '.handle',
            animation: 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            fallbackTolerance: 3,
            
            onChoose: function(evt) {
                console.log('🎯 Slide seleccionado para arrastrar:', evt.item.getAttribute('data-slide-id'));
                
                isDragging = true;
                draggedElement = evt.item;
                
                // Guardar orden original para rollback si es necesario
                originalOrder = Array.from(sortableElement.querySelectorAll('.slide-row')).map(row => ({
                    id: row.getAttribute('data-slide-id'),
                    orden: row.querySelector('.order-number').textContent.trim()
                }));
                
                console.log('📋 Orden original guardado:', originalOrder);
            },
            
            onStart: function(evt) {
                console.log('🚀 Iniciando arrastre de slide...');
                
                // Añadir clase visual
                evt.item.classList.add('dragging');
            },
            
            onMove: function(evt) {
                // Todos los slides pueden moverse entre cualquier posición
                return true;
            },
            
            onEnd: function(evt) {
                console.log('🏁 Finalizando arrastre...');
                console.log(`📊 Movimiento: ${evt.oldIndex} → ${evt.newIndex}`);
                
                isDragging = false;
                
                // Limpiar clases visuales
                evt.item.classList.remove('dragging');
                
                // Solo procesar si la posición realmente cambió
                if (evt.oldIndex !== evt.newIndex) {
                    console.log('✅ Posición cambió - Procesando actualización...');
                    processSlideOrder();
                } else {
                    console.log('⚠️ Sin cambio de posición detectado');
                }
                
                // Limpiar variables
                draggedElement = null;
            }
        });
    }

    // ==========================================
    // PROCESAMIENTO DE ORDEN
    // ==========================================
    
    /**
     * Procesa el nuevo orden de los slides y envía al servidor
     */
    function processSlideOrder() {
        console.log('⚙️ Procesando nuevo orden de slides...');
        
        setTimeout(() => {
            const updates = [];
            const tbody = document.getElementById('sortable-slides');
            
            if (!tbody) {
                console.error('❌ tbody sortable-slides no encontrado');
                return;
            }
            
            const slideRows = tbody.querySelectorAll('tr.slide-row');
            console.log(`📋 Procesando ${slideRows.length} slides`);
            
            slideRows.forEach((slideRow, index) => {
                const slideId = parseInt(slideRow.getAttribute('data-slide-id'));
                const newOrder = index + 1;
                
                updates.push({
                    id: slideId,
                    orden: newOrder
                });
                
                console.log(`🎬 Slide ID:${slideId} → orden:${newOrder}`);
            });
            
            if (updates.length > 0) {
                updateSlideStructure(updates);
            } else {
                console.warn('⚠️ No se generaron actualizaciones');
            }
        }, 200);
    }

    /**
     * Envía las actualizaciones de orden al servidor
     * @param {Array} updates - Array de actualizaciones
     */
    function updateSlideStructure(updates) {
        console.log('📤 Enviando actualizaciones al servidor...');
        console.table(updates);
        
        $.ajax({
            url: window.routes?.slideUpdateOrder || '/admin/slides/update-order',
            method: 'POST',
            data: {
                slides: updates,
                _token: window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                console.log('📤 Enviando actualización...');
                showLoadingIndicator();
            },
            success: function(response) {
                console.log('✅ Respuesta del servidor:', response);
                hideLoadingIndicator();
                
                if (typeof response === 'object' && response.success) {
                    console.log('🎉 Confirmación: Los slides se reordenaron correctamente');
                    
                    // Actualizar la visualización del orden
                    updateOrderDisplay();
                    
                    // Mostrar mensaje de éxito (opcional)
                    showSuccessMessage('Orden de slides actualizado correctamente');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error en la actualización:');
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                
                hideLoadingIndicator();
                
                // Restaurar orden original
                restoreOriginalOrder();
                
                showErrorMessage('Error al actualizar el orden de slides. Orden restaurado.');
            }
        });
    }

    /**
     * Actualiza la visualización del orden en la interfaz
     */
    function updateOrderDisplay() {
        console.log('🔄 Actualizando visualización del orden...');
        
        const tbody = document.getElementById('sortable-slides');
        const slideRows = tbody.querySelectorAll('tr.slide-row');
        
        slideRows.forEach((slideRow, index) => {
            const newOrder = index + 1;
            
            // Actualizar el número de orden mostrado
            const orderNumber = slideRow.querySelector('.order-number');
            if (orderNumber) {
                orderNumber.textContent = newOrder;
                console.log(`📝 Actualizada visualización: Slide ${slideRow.getAttribute('data-slide-id')} → orden ${newOrder}`);
            }
        });
        
        console.log('✨ Visualización actualizada - Ya no necesitas recargar la página');
    }

    /**
     * Restaura el orden original en caso de error
     */
    function restoreOriginalOrder() {
        console.log('🔄 Restaurando orden original...');
        
        if (originalOrder.length === 0) {
            console.warn('⚠️ No hay orden original para restaurar');
            return;
        }
        
        const tbody = document.getElementById('sortable-slides');
        const slideRows = Array.from(tbody.querySelectorAll('tr.slide-row'));
        
        // Reordenar según el orden original
        originalOrder.forEach(originalItem => {
            const slideRow = slideRows.find(row => 
                row.getAttribute('data-slide-id') === originalItem.id.toString()
            );
            
            if (slideRow) {
                tbody.appendChild(slideRow);
                const orderNumber = slideRow.querySelector('.order-number');
                if (orderNumber) {
                    orderNumber.textContent = originalItem.orden;
                }
            }
        });
        
        console.log('✅ Orden original restaurado');
    }

    // ==========================================
    // GESTIÓN DE ELIMINACIÓN
    // ==========================================
    
    /**
     * Inicializa los manejadores de eliminación
     */
    function initializeDeleteHandlers() {
        console.log('🗑️ Inicializando gestión de eliminación...');
        
        // Manejar click en botón eliminar
        $(document).on('click', '.btn-eliminar', function() {
            console.log('🗑️ Botón eliminar clickeado');
            
            const slideId = $(this).data('slide-id');
            const slideTitle = $(this).data('slide-title');
            
            console.log('Slide a eliminar:', { id: slideId, title: slideTitle });
            
            if (!slideId) {
                console.error('❌ No se encontró el slide-id en el botón');
                showErrorMessage('Error: No se pudo identificar el slide a eliminar');
                return;
            }
            
            // Configurar modal de confirmación
            const deleteUrl = window.routes?.slideIndex ? 
                window.routes.slideIndex + '/' + slideId : 
                '/admin/slides/' + slideId;
            
            $('#deleteForm').attr('action', deleteUrl);
            $('#slideToDelete').text(slideTitle || 'Slide #' + slideId);
            
            console.log('URL de eliminación:', deleteUrl);
            
            // Mostrar modal
            if ($('#deleteModal').length === 0) {
                console.error('❌ Modal de eliminación no encontrado');
                showErrorMessage('Error: Modal de eliminación no disponible');
                return;
            }
            
            $('#deleteModal').modal('show');
            console.log('✅ Modal de eliminación mostrado');
        });

        // Confirmar eliminación
        $('#confirmarEliminar').click(function() {
            console.log('🔥 Confirmar eliminación clickeado');
            
            const form = $('#deleteForm');
            const action = form.attr('action');
            
            console.log('Enviando eliminación a:', action);
            
            if (!action || action === '') {
                console.error('❌ No hay URL de acción en el formulario');
                showErrorMessage('Error: No se pudo configurar la eliminación');
                return;
            }
            
            // Mostrar indicador de carga
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');
            
            form.submit();
            console.log('✅ Formulario de eliminación enviado');
        });
    }

    // ==========================================
    // UTILIDADES UI
    // ==========================================
    
    /**
     * Muestra indicador de carga
     */
    function showLoadingIndicator() {
        // Deshabilitar handles durante la actualización
        $('.handle').css('pointer-events', 'none').css('opacity', '0.5');
        
        // Mostrar spinner en el botón de guardar si existe
        const saveButton = $('#saveOrder');
        if (saveButton.length) {
            saveButton.show().prop('disabled', true)
                     .html('<i class="fas fa-spinner fa-spin"></i> Actualizando...');
        }
    }

    /**
     * Oculta indicador de carga
     */
    function hideLoadingIndicator() {
        // Reactivar handles
        $('.handle').css('pointer-events', '').css('opacity', '');
        
        // Ocultar botón de guardar
        const saveButton = $('#saveOrder');
        if (saveButton.length) {
            saveButton.hide().prop('disabled', false)
                     .html('<i class="fas fa-save"></i> Guardar Orden');
        }
    }

    /**
     * Muestra mensaje de éxito
     * @param {string} message - Mensaje a mostrar
     */
    function showSuccessMessage(message) {
        // Implementar toast o notificación
        console.log('✅ ' + message);
        
        // Si tienes sistema de notificaciones, usarlo aquí
        // Ejemplo: toastr.success(message);
    }

    /**
     * Muestra mensaje de error
     * @param {string} message - Mensaje de error
     */
    function showErrorMessage(message) {
        console.error('❌ ' + message);
        alert(message); // Temporal, reemplazar con sistema de notificaciones
    }

    // ==========================================
    // DEBUG Y LOGGING
    // ==========================================
    
    // Debug: mostrar estado inicial
    console.log('=== ESTADO INICIAL ===');
    const initialSlides = document.querySelectorAll('.slide-row');
    console.log(`📋 ${initialSlides.length} slides encontrados`);
    
    initialSlides.forEach((slide, index) => {
        const id = slide.getAttribute('data-slide-id');
        const order = slide.querySelector('.order-number')?.textContent;
        console.log(`${index + 1}. Slide ID:${id} orden:${order}`);
    });
    
    console.log('✅ Sistema de gestión de slides inicializado correctamente');
});