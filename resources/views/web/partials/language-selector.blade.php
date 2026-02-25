{{-- Selector de idiomas para el frontend --}}
<div class="language-selector">
    @php
        $idiomasDisponibles = idiomas_disponibles();
        $idiomaActual = idioma_actual();
    @endphp
    
    @if(count($idiomasDisponibles) > 1)
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
                    type="button" 
                    id="languageDropdown" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                @php
                    $current = collect($idiomasDisponibles)->firstWhere('etiqueta', $idiomaActual);
                @endphp
                
                @if($current && $current['imagen_url'])
                    <img src="{{ $current['imagen_url'] }}" 
                         alt="{{ $current['nombre'] }}" 
                         class="language-flag me-1"
                         style="width: 20px; height: 15px; object-fit: cover;">
                @else
                    <i class="fas fa-globe me-1"></i>
                @endif
                
                <span class="language-name">{{ $current['nombre'] ?? 'Idioma' }}</span>
            </button>
            
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                @foreach($idiomasDisponibles as $idioma)
                    <li>
                        <a class="dropdown-item {{ es_idioma_actual($idioma['etiqueta']) ? 'active' : '' }}" 
                           href="{{ $idioma['url'] }}">
                            @if($idioma['imagen_url'])
                                <img src="{{ $idioma['imagen_url'] }}" 
                                     alt="{{ $idioma['nombre'] }}" 
                                     class="language-flag me-2"
                                     style="width: 20px; height: 15px; object-fit: cover;">
                            @else
                                <i class="fas fa-flag me-2 text-muted"></i>
                            @endif
                            
                            <span class="language-name">{{ $idioma['nombre'] }}</span>
                            
                            @if($idioma['es_principal'])
                                <small class="text-muted ms-1">(principal)</small>
                            @endif
                            
                            @if(es_idioma_actual($idioma['etiqueta']))
                                <i class="fas fa-check ms-auto text-success"></i>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        {{-- Si solo hay un idioma, mostrar información sin selector --}}
        @if(count($idiomasDisponibles) === 1)
            @php $unico = $idiomasDisponibles[0]; @endphp
            <div class="language-info">
                @if($unico['imagen_url'])
                    <img src="{{ $unico['imagen_url'] }}" 
                         alt="{{ $unico['nombre'] }}" 
                         class="language-flag me-1"
                         style="width: 20px; height: 15px; object-fit: cover;">
                @else
                    <i class="fas fa-globe me-1"></i>
                @endif
                <span class="language-name">{{ $unico['nombre'] }}</span>
            </div>
        @endif
    @endif
</div>

<style>
.language-selector .dropdown-item.active {
    background-color: #007bff;
    color: white;
}

.language-selector .language-flag {
    border-radius: 2px;
    border: 1px solid #dee2e6;
}

.language-selector .dropdown-item:hover .language-flag {
    border-color: #007bff;
}

.language-info {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #6c757d;
}

/* Versión horizontal para navegación principal */
.language-selector.horizontal {
    display: flex;
    gap: 0.5rem;
}

.language-selector.horizontal .dropdown {
    position: static;
}

/* Versión vertical para sidebar */
.language-selector.vertical .dropdown-menu {
    min-width: 200px;
}
</style>