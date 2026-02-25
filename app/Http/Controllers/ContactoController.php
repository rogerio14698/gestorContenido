<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class ContactoController extends Controller
{
    public function form()
    {
        $configuracion = \App\Models\Configuracion::first();
        $menus = \App\Models\Menu::principal()->where('visible', true)->get();
        return view('web.contacto', compact('configuracion', 'menus'));
    }

    public function enviar(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefono' => 'nullable|string|max:30',
            'mensaje' => 'required|string',
        ]);

        // Aquí puedes enviar el correo o guardar en base de datos
        // Ejemplo: enviar correo a la empresa
        
        Mail::to(config('mail.from.address'))
            ->send(new ContactoRecibido($validated));
        

        Session::flash('success', __('¡Mensaje enviado correctamente! Nos pondremos en contacto contigo.'));
        return redirect()->route('contacto.form');
    }
}

