<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oferta;
use App\Models\Paso;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Pais;

class OfertaController extends Controller
{
    public function index()
    {
        // Obtiene todas las ofertas para mostrarlas en la vista
        $ofertas = Oferta::all();
        return view('home', compact('ofertas'));
    }

    public function create()
    {
        // Muestra el formulario para crear una nueva oferta
        return view('home'); // Asegúrate de que el modal esté incluido en esta vista
    }

    public function store(Request $request)
{
    // Valida los datos de entrada
    $request->validate([
        'nombre' => 'required|string|max:255|unique:ofertas,nombre',
        'titulo' => 'required|string|max:255',
        'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'paises.*.nombre' => 'nullable|string|max:255',
        'paises.*.link' => 'nullable|url',
        'pasos.*.descripcion' => 'nullable|string',
        'pasos.*.orden' => 'required|integer',
    ]);

    try {
        // Maneja la carga del archivo de logo
        $logoPath = $request->file('logo')->store('logos', 'public');

        // Crea la oferta
        $oferta = Oferta::create([
            'nombre' => $request->nombre,
            'titulo' => $request->titulo,
            'logo' => $logoPath,
        ]);

        // Crea los pasos asociados a la oferta
        foreach ($request->input('pasos', []) as $paso) {
            Paso::create([
                'oferta_id' => $oferta->id,
                'orden' => $paso['orden'],
                'descripcion' => $paso['descripcion'],
            ]);
        }

        // Crea los países asociados a la oferta
        foreach ($request->input('paises', []) as $pais) {
            Pais::create([
                'oferta_id' => $oferta->id,
                'nombre' => $pais['nombre'],
                'link' => $pais['link'],
            ]);
        }

        return redirect()->back()->with('success', "Oferta Creada Exitosamente");

    } catch (\Exception $e) {
        Log::error($e);
        Log::error($e->getMessage());
        // Maneja cualquier error que pueda ocurrir
        return redirect()->back()->withErrors(['error' => `Ocurrio un error $e` ])->withInput();
    }
}

public function update(Request $request, $id)
{
    try {
        // Filtra los pasos y países para eliminar los vacíos
        $filteredPasos = array_filter($request->input('pasos', []), function ($paso) {
            return !empty($paso['descripcion']);
        });

        $filteredPaises = array_filter($request->input('paises', []), function ($pais) {
            return !empty($pais['nombre']) && !empty($pais['link']);
        });

        // Encuentra la oferta por ID
        $oferta = Oferta::findOrFail($id);

        // Valida los datos de entrada
        $request->validate([
            'titulo' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'pasos.*.descripcion' => 'nullable|string',
            'pasos.*.orden' => 'nullable|integer',
            'paises.*.nombre' => 'nullable|string|max:255',
            'paises.*.link' => 'nullable|url',
        ]);

        // Actualiza la oferta
        $oferta->update([
            'titulo' => $request->titulo
        ]);

        // Actualiza el logo si se ha proporcionado uno nuevo
        if ($request->hasFile('logo')) {
            // Borra el logo anterior si existe
            if ($oferta->logo) {
                Storage::disk('public')->delete($oferta->logo);
            }
            $logoPath = $request->file('logo')->store('logos', 'public');
            $oferta->update(['logo' => $logoPath]);
        }

        // Elimina los pasos antiguos
        Paso::where('oferta_id', $oferta->id)->delete();
        // Actualiza los pasos asociados a la oferta
        foreach ($filteredPasos as $paso) {
            Paso::create([
                'oferta_id' => $oferta->id,
                'orden' => $paso['orden'],
                'descripcion' => $paso['descripcion'],
            ]);
        }

        // Elimina los países antiguos
        Pais::where('oferta_id', $oferta->id)->delete();
        // Actualiza los países asociados a la oferta
        foreach ($filteredPaises as $pais) {
            Pais::create([
                'oferta_id' => $oferta->id,
                'nombre' => $pais['nombre'],
                'link' => $pais['link'],
            ]);
        }

        return redirect()->route('home')->with('status', 'Oferta Actualizada con éxito.');
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Manejo de errores de validación
        return redirect()->route('home')->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        // Manejo de otros errores
        return redirect()->route('home')->with('error', "Hubo un problema al actualizar la oferta: " . $e->getMessage());
    }
}


    public function edit($id)
    {
        // Obtener la oferta y sus pasos para pasar a la vista de edición
        $oferta = Oferta::with('pasos')->findOrFail($id);
        return view('edit', compact('oferta'));
    }


    public function destroy($id)
    {
        Log::info('Eliminando oferta: ' . $id);
        // Encuentra la oferta por ID
        $oferta = Oferta::findOrFail($id);

        Log::info('Eliminando oferta: ' . $oferta->nombre);

        // Elimina la oferta y sus pasos asociados
        $oferta->pasos()->delete();
        $oferta->delete();

        return redirect()->route('home')->with('status', 'Oferta eliminada con éxito.');
    }

    public function ver($nombre)
{   
    // Encuentra la oferta por nombre, incluyendo los pasos y los países
    $oferta = Oferta::where('nombre', $nombre)->with(['pasos', 'paises'])->firstOrFail();

    // Pasa los datos a la vista
    return view('oferta.ver', [
        'titulo' => $oferta->titulo,
        'foto' => $oferta->logo,
        'pasos' => $oferta->pasos,
        'paises' => $oferta->paises,
    ]);
}
}

