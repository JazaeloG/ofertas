@extends('layouts.app')

@section('content')
<style>
    /* Estilos para la tabla */
    .table thead th {
        background-color: #2a2a2a; /* Color morado */
        color: white;
        text-align: center;
    }

    .table tbody td {
        text-align: center;
    }

    .modal-header {
        background-color: #6f42c1;
        color: white;
    }

    .modal-footer .btn-primary {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }

    .btn-primary {
        background-color: #6f42c1;
        border-color: #6f42c1;
    }

    .btn-primary:hover {
        background-color: #5a379e; 
        border-color: #5a379e;
    }
    
</style>

<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Ofertas Activas') }}
                    <!-- Botón para abrir el modal -->
                    <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modal-crear">
                        Agregar
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Titulo</th>
                                <th>Descripcion</th>
                                <th>Logo</th>
                                <th>Pais(es) & Enlace</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ofertas as $oferta)
                                <tr>
                                    <td>{{ $oferta->nombre }}</td>
                                    <td>{{ $oferta->titulo }}</td>
                                    <td>
                                        @foreach ($oferta->pasos as $paso)
                                            ✅ {{ $paso->descripcion }}<br>
                                        @endforeach
                                    </td>
                                    <td><img src="{{ asset('storage/' . $oferta->logo) }}" alt="Logo" class="img-fluid" style="max-width: 50px;"></td>
                                    <td>
                                        @foreach ($oferta->paises as $pais)
                                            <div>
                                                {{ $pais->nombre }} - <a href="{{ $pais->link }}" target="_blank">{{ $pais->link }}</a>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <form action="{{ route('ofertas.edit', $oferta->id) }}" method="GET" style="display:inline;">
                                            <button type="submit" class="btn btn-primary btn-sm">Editar</button>
                                        </form>
                                        <form action="{{ route('ofertas.destroy', $oferta->id) }}" method="POST" style="display:inline;" id="delete-form-{{ $oferta->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $oferta->id }})">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear -->
<div class="modal fade" id="modal-crear" tabindex="-1" aria-labelledby="modal-crear-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-crear-label">Crear Nuevo Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="crear-form" method="POST" action="{{ route('ofertas.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
                    </div>
                    <!-- Sección de Países -->
                    <div id="paises-container">
                        <label class="form-label">Países</label>
                        <div id="paises-list">
                            <!-- Campo de país vacío para empezar -->
                            <div class="mb-3">
                                <label for="pais-0-nombre" class="form-label">País 1 Nombre</label>
                                <input type="text" class="form-control pais-nombre-input" id="pais-0-nombre" name="paises[0][nombre]">
                                <label for="pais-0-link" class="form-label">País 1 URL</label>
                                <input type="url" class="form-control pais-link-input" id="pais-0-link" name="paises[0][link]">
                            </div>
                        </div>
                    </div>
                    <!-- Sección de Pasos -->
                    <div id="pasos-container">
                        <label class="form-label">Pasos</label>
                        <div id="pasos-list">
                            <!-- Campo de paso vacío para empezar -->
                            <div class="mb-3">
                                <label for="paso-0" class="form-label">Paso 1</label>
                                <input type="text" class="form-control paso-input" id="paso-0" name="pasos[0][descripcion]">
                                <input type="hidden" name="pasos[0][orden]" value="0">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3 float-end">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateStepNumbers() {
            const pasosList = document.getElementById('pasos-list');
            const pasos = pasosList.querySelectorAll('div.mb-3');
            pasos.forEach((paso, index) => {
                const pasoLabel = paso.querySelector('label');
                const pasoInput = paso.querySelector('input.paso-input');
                const pasoHidden = paso.querySelector('input[type="hidden"]');
                if (pasoLabel && pasoInput && pasoHidden) {
                    pasoLabel.textContent = `Paso ${index + 1}`;
                    pasoInput.id = `paso-${index}`;
                    pasoInput.name = `pasos[${index}][descripcion]`;
                    pasoHidden.name = `pasos[${index}][orden]`;
                    pasoHidden.value = index;
                }
            });
        }

        function updateCountryNumbers() {
            const paisesList = document.getElementById('paises-list');
            const paises = paisesList.querySelectorAll('div.mb-3');
            paises.forEach((pais, index) => {
                const nombreLabel = pais.querySelector('label[for$="nombre"]');
                const nombreInput = pais.querySelector('input.pais-nombre-input');
                const linkLabel = pais.querySelector('label[for$="link"]');
                const linkInput = pais.querySelector('input.pais-link-input');
                if (nombreLabel && nombreInput && linkLabel && linkInput) {
                    nombreLabel.textContent = `País ${index + 1} Nombre`;
                    nombreInput.id = `pais-${index}-nombre`;
                    nombreInput.name = `paises[${index}][nombre]`;
                    linkLabel.textContent = `País ${index + 1} URL`;
                    linkInput.id = `pais-${index}-link`;
                    linkInput.name = `paises[${index}][link]`;
                }
            });
        }

        function addNewStep() {
            const lastPaso = document.querySelector('#pasos-list > div.mb-3:last-child input.paso-input');
            const pasosList = document.getElementById('pasos-list');
            if (lastPaso && lastPaso.value.trim() !== '') {
                const pasoIndex = pasosList.querySelectorAll('div.mb-3').length;
                const pasoHTML = `
                    <div class="mb-3">
                        <label for="paso-${pasoIndex}" class="form-label">Paso ${pasoIndex + 1}</label>
                        <input type="text" class="form-control paso-input" id="paso-${pasoIndex}" name="pasos[${pasoIndex}][descripcion]">
                        <input type="hidden" name="pasos[${pasoIndex}][orden]" value="${pasoIndex}">
                    </div>
                `;
                pasosList.insertAdjacentHTML('beforeend', pasoHTML);
                updateStepNumbers(); // Actualizar numeración
            }
        }

        function addNewCountry() {
            const lastPaisNombre = document.querySelector('#paises-list > div.mb-3:last-child input.pais-nombre-input');
            const paisesList = document.getElementById('paises-list');
            if (lastPaisNombre && lastPaisNombre.value.trim() !== '') {
                const paisIndex = paisesList.querySelectorAll('div.mb-3').length;
                const paisHTML = `
                    <div class="mb-3">
                        <label for="pais-${paisIndex}-nombre" class="form-label">País ${paisIndex + 1} Nombre</label>
                        <input type="text" class="form-control pais-nombre-input" id="pais-${paisIndex}-nombre" name="paises[${paisIndex}][nombre]">
                        <label for="pais-${paisIndex}-link" class="form-label">País ${paisIndex + 1} URL</label>
                        <input type="url" class="form-control pais-link-input" id="pais-${paisIndex}-link" name="paises[${paisIndex}][link]">
                    </div>
                `;
                paisesList.insertAdjacentHTML('beforeend', paisHTML);
                updateCountryNumbers(); // Actualizar numeración
            }
        }

        function removeEmptyFields() {
            const pasosList = document.getElementById('pasos-list');
            const paisesList = document.getElementById('paises-list');
            // Eliminar campos de pasos vacíos
            pasosList.querySelectorAll('div.mb-3').forEach((paso, index) => {
                const pasoInput = paso.querySelector('input.paso-input');
                if (pasoInput && pasoInput.value.trim() === '') {
                    paso.remove();
                }
            });
            // Eliminar campos de países vacíos
            paisesList.querySelectorAll('div.mb-3').forEach((pais, index) => {
                const paisNombreInput = pais.querySelector('input.pais-nombre-input');
                if (paisNombreInput && paisNombreInput.value.trim() === '') {
                    pais.remove();
                }
            });
            updateStepNumbers(); // Actualizar numeración después de eliminar campos vacíos
            updateCountryNumbers(); // Actualizar numeración después de eliminar campos vacíos
        }

        // Añadir campos dinámicamente cuando el usuario deje de escribir
        document.getElementById('pasos-list').addEventListener('change', function (event) {
            if (event.target && event.target.classList.contains('paso-input')) {
                addNewStep();
            }
        });

        document.getElementById('paises-list').addEventListener('change', function (event) {
            if (event.target && event.target.classList.contains('pais-nombre-input')) {
                addNewCountry();
            }
        });

        document.getElementById('crear-form').addEventListener('submit', function (event) {
            removeEmptyFields();
        });

        // Inicialmente agregar un nuevo paso y país si el último campo no está vacío
        addNewStep();
        addNewCountry();
    });
</script>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¡Esta acción no se puede deshacer!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6f42c1',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }
</script>
@endsection
