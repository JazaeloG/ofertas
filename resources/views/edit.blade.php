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
    .card-header {
        background-color: #6f42c1;
        color: white;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Editar Oferta') }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('ofertas.update', $oferta->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $oferta->nombre }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" value="{{ $oferta->titulo }}" required>
                        </div>
                        <!-- Sección de Países -->
                        <div id="paises-container">
                            <label class="form-label">Países</label>
                            <div id="paises-list">
                                @foreach($oferta->paises as $index => $pais)
                                    <div class="mb-3">
                                        <label for="pais-{{ $index }}-nombre" class="form-label">País {{ $index + 1 }} Nombre</label>
                                        <input type="text" class="form-control pais-nombre-input" id="pais-{{ $index }}-nombre" name="paises[{{ $index }}][nombre]" value="{{ $pais->nombre }}">
                                        <label for="pais-{{ $index }}-link" class="form-label">País {{ $index + 1 }} URL</label>
                                        <input type="url" class="form-control pais-link-input" id="pais-{{ $index }}-link" name="paises[{{ $index }}][link]" value="{{ $pais->link }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Sección de Pasos -->
                        <div id="pasos-container">
                            <label class="form-label">Pasos</label>
                            <div id="pasos-list">
                                @foreach($oferta->pasos as $index => $paso)
                                    <div class="mb-3">
                                        <label for="paso-{{ $index }}" class="form-label">Paso {{ $index + 1 }}</label>
                                        <input type="text" class="form-control paso-input" id="paso-{{ $index }}" name="pasos[{{ $index }}][descripcion]" value="{{ $paso->descripcion }}">
                                        <input type="hidden" name="pasos[{{ $index }}][orden]" value="{{ $index }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 float-end">Guardar</button>
                    </form>
                </div>
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

        document.querySelector('form').addEventListener('submit', function (event) {
            removeEmptyFields();
        });

        // Inicialmente agregar un nuevo paso y país si el último campo no está vacío
        addNewStep();
        addNewCountry();
    });
</script>
@endsection