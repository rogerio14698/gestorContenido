@php use Illuminate\Support\Facades\Storage; @endphp
<footer >
    <div class="container">

            <div class="col-lg-3">
                {{-- Protecciones: algunas instalaciones pueden no tener registro en configuracion_empresa --}}
                @php
                    $idiomaEtiqueta = app()->getLocale();
                    $textoEmpresa = null;
                    if (!empty($configEmpresa) && !empty($configEmpresa->textos) && $configEmpresa->textos->count() > 0) {
                        $textoEmpresa = $configEmpresa->textos->first(function($t) use ($idiomaEtiqueta) {
                            return optional($t->idioma)->etiqueta === $idiomaEtiqueta;
                        }) ?? $configEmpresa->textos->first();
                    }
                @endphp
                <h5 class="text-uppercase fw-bold mb-3">{{ optional($configEmpresa)->nombre ?? '' }}</h5>
                <p class="mb-0">{{ optional($textoEmpresa)->metadescripcion ?? '' }}</p>
            </div>
            <div class="col-lg-3">
                <a href="#">
                    <img src="{{ asset('images/logo.png') }}" alt="Nun Tris Teatro" height="48">
                </a>
               
            </div>
            <div class="col-lg-3">
                <nav>
                    <ul>
                    @foreach(($menusPie ?? []) as $item)
                        <li><a href="{{ menu_url($item, $idiomaRuta) }}">{{ $item->titulo }}</a></li>
                    @endforeach
                    </ul>
                </nav>
            </div>
            <div class="col-lg-3">
               
                <ul class="info-empresa">
                    <li>{{ optional($configEmpresa)->metatitulo ?? '' }}</li>
                    <li><a href="tel:{{ optional($configEmpresa)->telefono ?? '' }}">{{ optional($configEmpresa)->telefono ?? '' }}</a></li>
                    <li><a href="mailto:{{ optional($configEmpresa)->email ?? '' }}">{{ optional($configEmpresa)->email ?? '' }}</a></li>
                </ul>
              
                    
                    @if(!empty($redesSociales))
                        <ul class="redes">
                            @foreach($redesSociales as $red)
                                @if(!empty($red['url']) && !empty($red['icono']))
                                    <li>
                                        <a href="{{ $red['url'] }}" target="_blank" rel="noopener">
                                            <img src="{{ Storage::url($red['icono']) }}" alt="{{ $red['alt'] ?? '' }}">
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
             
            </div><!-- //.col-lg-4 -->

    </div><!-- //.container -->



</footer>
    <div class="cierre-footer-container">
        <div class="row">  
            <p> &copy; {{ now()->year }} {{ optional($configEmpresa)->nombre ?? 'Nuntris Teatro' }} · {{ __('Todos los derechos reservados') }}</p>
        </div><!-- //.row -->
        </div><!-- //.container -->