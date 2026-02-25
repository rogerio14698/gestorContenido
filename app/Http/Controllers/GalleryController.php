<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Mostrar todas las galerías públicas
     */
    public function index()
    {
        $galleries = Gallery::where('activa', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('galleries.index', compact('galleries'));
    }

    /**
     * Mostrar una galería específica
     */
    public function show($slug)
    {
        $gallery = Gallery::where('activa', true)
            ->with(['images' => function($query) {
                $query->with(['texts.idioma'])
                      ->orderBy('orden')
                      ->orderBy('id');
            }])
            ->get()
            ->firstWhere('slug', $slug);

        if (!$gallery) {
            abort(404);
        }

        return view('galleries.show', compact('gallery'));
    }
}