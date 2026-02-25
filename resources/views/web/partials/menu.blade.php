{{-- Menú principal --}}
<nav class="navbar navbar-expand-lg navbar-dark ">
    <div class="container">
   <a class="navbar-brand" href="{{ route('inicio', ['idioma' => $idiomaRuta]) }}">
                    <span><img src="{{ asset('images/logo.png') }}" alt="Logo Nun Tris Teatro"></span>
                </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
                <div class="collapse navbar-collapse" id="primaryNav">
                    <ul class="navbar-nav ms-auto  gap-lg-3">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('inicio', ['idioma' => $idiomaRuta]) }}">{{ __('Inicio') }}</a>
                        </li>

                        @foreach($menusCollection as $menu)
                            @php
                                $children = ($menu->children ?? collect())->filter(fn($child) => (bool) $child->visible);
                                $hasChildren = $children->isNotEmpty();
                                $menuTitle = trim($menu->titulo ?? '');
                                $menuLink = menu_url($menu, $idiomaActual);
                                $menuTarget = $menu->abrir_nueva_ventana ? '_blank' : '_self';
                            @endphp

                            @if($menuTitle !== '')
                                <li class="nav-item {{ $hasChildren ? 'dropdown' : '' }}">
                                    @if($hasChildren)
                                        <a class="nav-link dropdown-toggle" href="{{ $menuLink }}" role="button" data-bs-toggle="dropdown" aria-expanded="false" target="{{ $menuTarget }}">
                                            {{ $menuTitle }}
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                            @foreach($children as $child)
                                                @php
                                                    $childTitle = trim($child->titulo ?? '');
                                                    $childLink = menu_url($child, $idiomaActual);
                                                    $childTarget = $child->abrir_nueva_ventana ? '_blank' : '_self';
                                                @endphp
                                                @if($childTitle !== '')
                                                    <li><a class="dropdown-item" href="{{ $childLink }}" target="{{ $childTarget }}">{{ $childTitle }}</a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        <a class="nav-link" href="{{ $menuLink }}" target="{{ $menuTarget }}">{{ $menuTitle }}</a>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contacto.form') }}">
                                <i class="fa-solid fa-envelope me-1"></i>{{ __('Contacto') }}
                            </a>
                        </li>



                        <li class="nav-item mt-3 mt-lg-0">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-light btn-language dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ strtoupper($idiomaRuta) }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                    @foreach($idiomasDisponibles as $idioma)
                                        <li>
                                            <a class="dropdown-item {{ es_idioma_actual($idioma['etiqueta']) ? 'active fw-semibold' : '' }}" href="{{ $idioma['url'] }}">
                                                {{ $idioma['nombre'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div><!-- //.dropdown -->
                        </li>
                    </ul>
                </div><!-- //.collapse.navbar-collapse -->
    </div><!-- //.container -->
</nav>