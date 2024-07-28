<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        body {
            background-color: #9370DB; /* Stronger purple background */
        }
        .step-text {
            color: white;
        }

        .text-brand {
            padding-top: 10px;
            color: white;
            font-weight: 600;
        }
        .btn-custom {
            background-color: rgb(228, 228, 0);
            color: black; 
            width: 150px; 
            height: 40px; 
            display: flex;
            font-weight: 600;
            justify-content: space-between;
            align-items: center;
            margin: 5px auto; 
        }
        .btn-custom i {
            margin-left: 10px; 
        }

        .btn-custom:hover {
            background-color: rgb(255, 255, 0);
            color: black;
        }

        .title-brand {
            color: white;
            font-weight: 600;
            font-size: 3rem;
        }

        .card-grey {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body class="container-fluid">
    <div class="row mt-5">
        <div class="col-md-4 col-12 mx-auto text-center">
            <img src="{{ asset('storage/' . $foto) }}" width="100px"/>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-4 col-12 mx-auto text-center">
            <h4 class="title-brand">{{ $titulo }}</h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mx-auto col-12">
            <div class="card-grey">
                @foreach($pasos as $item)
                    <div class="row">
                        <div class="col-12 text-justify">
                            <p class="text-justify step-text">✅ {{ $item->descripcion }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="row">
        <p class="col-md-12 text-center text-brand">Para más información, consulte los siguientes enlaces</p>
        <div class="col-md-8 col-12 mx-auto text-center">
            @foreach($paises as $pais)
                <p class="text-center">
                    <a class="btn btn-custom" href="{{ $pais->link }}">
                        {{ $pais->nombre }}
                        <i class="fa fa-external-link"></i>
                    </a>
                </p>
            @endforeach
        </div>
    </div>
</body>
</html>