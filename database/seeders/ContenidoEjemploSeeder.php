<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\TextoIdioma;
use App\Models\Menu;
use App\Models\Galeria;
use App\Models\Idioma;
use App\Models\TipoContenido;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContenidoEjemploSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener idiomas
        $idiomaEs = Idioma::where('codigo', 'es')->first();
        $idiomaAs = Idioma::where('codigo', 'as')->first();
        
        // Obtener tipos de contenido
        $tipoContenido = TipoContenido::where('tipo_contenido', 'Contenido')->first();
        $tipoNoticias = TipoContenido::where('tipo_contenido', 'Noticias')->first();
        
        // Crear galería de ejemplo
        $galeria = Galeria::create([
            'carpeta' => 'inicio',
            'orden' => 1,
        ]);

        // 1. Crear página de inicio
        $contenidoInicio = Content::create([
            'tipo_contenido' => 'pagina',
            'pagina_estatica' => true,
            'actions' => 'inicio',
            'galeria_id' => $galeria->id,
        ]);

        // Textos en español
        TextoIdioma::create([
            'idioma_id' => $idiomaEs->id,
            'contenido_id' => $contenidoInicio->id,
            'tipo_contenido_id' => $tipoContenido->id,
            'titulo' => 'Bienvenidos a Nuntris Teatro',
            'subtitulo' => 'Compañía de Teatro Asturiana',
            'resumen' => 'Descubre la magia del teatro asturiano con Nuntris Teatro, una compañía comprometida con la cultura y las tradiciones.',
            'contenido' => '<p>Nuntris Teatro es una compañía teatral asturiana dedicada a la promoción y difusión del teatro en lengua asturiana y castellana. Fundada en 2010, hemos llevado nuestras obras por toda Asturias y más allá.</p>

<h3>Nuestra Misión</h3>
<p>Promover el teatro como herramienta cultural y educativa, preservando las tradiciones asturianas mientras exploramos nuevas formas de expresión artística.</p>

<h3>¿Qué Hacemos?</h3>
<ul>
    <li>Representaciones teatrales en asturiano y castellano</li>
    <li>Talleres de formación teatral</li>
    <li>Actividades culturales y educativas</li>
    <li>Colaboraciones con instituciones locales</li>
</ul>

<p>Te invitamos a conocer nuestro trabajo y formar parte de esta aventura teatral.</p>',
            'slug' => 'inicio',
            'visible' => true,
        ]);

        // Textos en asturiano
        TextoIdioma::create([
            'idioma_id' => $idiomaAs->id,
            'contenido_id' => $contenidoInicio->id,
            'tipo_contenido_id' => $tipoContenido->id,
            'titulo' => 'Bienveníos a Nuntris Teatro',
            'subtitulo' => 'Compañía de Teatru Asturiana',
            'resumen' => 'Descubri la maxia del teatru asturianu con Nuntris Teatro, una compañía comprometida cola cultura y les tradiciones.',
            'contenido' => '<p>Nuntris Teatro ye una compañía teatral asturiana adicada a la promoción y difusión del teatru en llingua asturiana y castellana. Fundada en 2010, llevamos les nueses obres per toa Asturies y más allá.</p>

<h3>La Nuesa Misión</h3>
<p>Promover el teatru como ferramienta cultural y educativa, calteniendo les tradiciones asturianes mentanto esploramos nueves formes d\'expresión artística.</p>

<h3>¿Qué Facemos?</h3>
<ul>
    <li>Representaciones teatrales n\'asturianu y castellanu</li>
    <li>Talleres de formación teatral</li>
    <li>Actividaes culturales y educatives</li>
    <li>Collaboraciones con instituciones llocales</li>
</ul>

<p>Convidámoste a conocer el nuesu trabayu y formar parte d\'esta aventura teatral.</p>',
            'slug' => 'entamu',
            'visible' => true,
        ]);

        // 2. Crear algunas noticias de ejemplo
        $noticias = [
            [
                'lugar' => 'Teatro Principal, Oviedo',
                'fecha' => now()->subDays(10),
                'fecha_publicacion' => now()->subDays(10),
                'portada' => true,
                'titulo_es' => 'Estreno de "La Casa de Bernarda Alba" en asturiano',
                'titulo_as' => 'Estrenu de "La Casa de Bernarda Alba" n\'asturianu',
                'resumen_es' => 'Gran éxito en el estreno de nuestra nueva adaptación de la obra de Lorca al asturiano.',
                'resumen_as' => 'Gran ésitu nel estrenu de la nuesa nueva adaptación de la obra de Lorca al asturianu.',
                'contenido_es' => '<p>El pasado viernes tuvimos el honor de estrenar nuestra nueva producción "La Casa de Bernarda Alba" en una magnífica adaptación al asturiano en el Teatro Principal de Oviedo.</p>

<p>La obra, dirigida por nuestro director artístico, cuenta la historia de una familia marcada por el autoritarismo y la represión, temas que cobran nueva vida en la versión asturiana.</p>

<h4>Reparto</h4>
<ul>
    <li>María González como Bernarda Alba</li>
    <li>Carmen Rodríguez como La Poncia</li>
    <li>Ana Fernández como Adela</li>
</ul>

<p>Las próximas funciones tendrán lugar los días 15, 16 y 17 de este mes. ¡No te las pierdas!</p>',
                'contenido_as' => '<p>El vienres pasáu tuvimos l\'honor d\'estrenar la nuesa nueva producción "La Casa de Bernarda Alba" nuna magnífica adaptación al asturianu nel Teatru Principal d\'Uviéu.</p>

<p>La obra, dirixida pol nuesu direutor artísticu, cuenta la historia d\'una familia marcada pol autoritarismu y la represión, temes que cobren nueva vida na versión asturiana.</p>

<h4>Repartu</h4>
<ul>
    <li>María González como Bernarda Alba</li>
    <li>Carmen Rodríguez como La Poncia</li>
    <li>Ana Fernández como Adela</li>
</ul>

<p>Les próximes funciones tendrán llugar los díes 15, 16 y 17 d\'esti mes. ¡Nun te les pierdas!</p>',
            ],
            [
                'lugar' => 'Sede de la compañía',
                'fecha' => now()->subDays(5),
                'fecha_publicacion' => now()->subDays(5),
                'portada' => true,
                'titulo_es' => 'Taller de teatro para jóvenes',
                'titulo_as' => 'Taller de teatru pa mozos',
                'resumen_es' => 'Abrimos inscripciones para nuestro nuevo taller de formación teatral dirigido a jóvenes de 14 a 25 años.',
                'resumen_as' => 'Abrimos inscripciones pal nuesu nuevu taller de formación teatral dirixíu a mozos de 14 a 25 años.',
                'contenido_es' => '<p>Estamos emocionados de anunciar la apertura de inscripciones para nuestro nuevo taller de teatro dirigido específicamente a jóvenes entre 14 y 25 años.</p>

<h4>¿Qué aprenderás?</h4>
<ul>
    <li>Técnicas básicas de interpretación</li>
    <li>Expresión corporal y vocal</li>
    <li>Improvisación teatral</li>
    <li>Historia del teatro asturiano</li>
</ul>

<h4>Información práctica</h4>
<p><strong>Duración:</strong> 3 meses (enero-marzo)<br>
<strong>Horario:</strong> Sábados de 10:00 a 13:00<br>
<strong>Precio:</strong> 60€/mes</p>

<p>Las plazas son limitadas. ¡Apúntate cuanto antes!</p>',
                'contenido_as' => '<p>Tamos emocionaos d\'anunciar l\'apertura d\'inscripciones pal nuesu nuevu taller de teatru dirixíu específicamente a mozos ente 14 y 25 años.</p>

<h4>¿Qué vas aprender?</h4>
<ul>
    <li>Técniques básiques d\'interpretación</li>
    <li>Expresión corporal y vocal</li>
    <li>Improvisación teatral</li>
    <li>Historia del teatru asturianu</li>
</ul>

<h4>Información práutica</h4>
<p><strong>Duración:</strong> 3 meses (xineru-marzu)<br>
<strong>Horariu:</strong> Sábaos de 10:00 a 13:00<br>
<strong>Preciu:</strong> 60€/mes</p>

<p>Les places son llimitaes. ¡Apúntate cuanto antes!</p>',
            ],
            [
                'lugar' => 'Gijón',
                'fecha' => now()->subDays(2),
                'fecha_publicacion' => now()->subDays(2),
                'portada' => false,
                'titulo_es' => 'Colaboración con el Festival de Teatro de Gijón',
                'titulo_as' => 'Collaboración col Festival de Teatru de Xixón',
                'resumen_es' => 'Nuntris Teatro participará en el próximo Festival de Teatro de Gijón con una obra especial.',
                'resumen_as' => 'Nuntris Teatro participará nel próximu Festival de Teatru de Xixón con una obra especial.',
                'contenido_es' => '<p>Tenemos el placer de anunciar que Nuntris Teatro ha sido seleccionada para participar en el prestigioso Festival de Teatro de Gijón 2024.</p>

<p>Presentaremos una obra inédita titulada "Vientu del Norte", una creación colectiva que fusiona elementos del teatro tradicional asturiano con técnicas contemporáneas.</p>

<p>Esta colaboración representa un paso importante en nuestro crecimiento como compañía y en la proyección del teatro asturiano a nivel nacional.</p>

<p>Más detalles sobre fechas y entradas próximamente.</p>',
                'contenido_as' => '<p>Tenemos el prestar d\'anunciar que Nuntris Teatro foi seleicionada pa participar nel prestixosu Festival de Teatru de Xixón 2024.</p>

<p>Presentaremos una obra inédita titulada "Vientu del Norte", una creación coleutiva que fusiona elementos del teatru tradicional asturianu con técniques contemporánees.</p>

<p>Esta collaboración representa un pasu importante nel nuesu crecimientu como compañía y na proyeición del teatru asturianu a nivel nacional.</p>

<p>Más detalles sobre feches y entraes próximamente.</p>',
            ],
        ];

        foreach ($noticias as $index => $noticia) {
            $content = Content::create([
                'lugar' => $noticia['lugar'],
                'fecha' => $noticia['fecha'],
                'fecha_publicacion' => $noticia['fecha_publicacion'],
                'tipo_contenido' => 'noticia',
                'portada' => $noticia['portada'],
                'orden' => $index + 1,
            ]);

            // Texto en español
            TextoIdioma::create([
                'idioma_id' => $idiomaEs->id,
                'contenido_id' => $content->id,
                'tipo_contenido_id' => $tipoNoticias->id,
                'titulo' => $noticia['titulo_es'],
                'resumen' => $noticia['resumen_es'],
                'contenido' => $noticia['contenido_es'],
                'slug' => \Str::slug($noticia['titulo_es']),
                'visible' => true,
            ]);

            // Texto en asturiano
            TextoIdioma::create([
                'idioma_id' => $idiomaAs->id,
                'contenido_id' => $content->id,
                'tipo_contenido_id' => $tipoNoticias->id,
                'titulo' => $noticia['titulo_as'],
                'resumen' => $noticia['resumen_as'],
                'contenido' => $noticia['contenido_as'],
                'slug' => \Str::slug($noticia['titulo_as']),
                'visible' => true,
            ]);
        }

        // Crear algunos elementos de menú
        $menuInicio = Menu::create([
            'title' => 'Inicio',
            'content_id' => $contenidoInicio->id,
            'order' => 1,
            'icon' => 'fas fa-home',
        ]);

        $this->command->info('Contenido de ejemplo creado exitosamente.');
    }
}
