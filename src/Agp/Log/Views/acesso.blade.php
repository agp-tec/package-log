@extends('layout.app')

@section('content')
    <div class="card card-custom gutter-b">
        <div class="card-header border-0 py-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label font-weight-bolder text-success">Acessos dos usuários</span>
                <span class="text-muted mt-3 font-weight-bold font-size-sm">Estes são os acessos dos usuários</span>
            </h3>
        </div>

        {{ Agp\Log\ViewComposer\LogComposer::getDatatableUsuarioAcesso() }}

    </div>
@endsection
