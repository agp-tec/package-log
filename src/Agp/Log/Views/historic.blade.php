<div class="card card-custom card-collapse mb-8" data-card="true">
    <div class="card-header">
        <div class="card-title">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label font-weight-bolder text-primary">Histórico</span>
            </h3>
        </div>
        <div class="card-toolbar">
            <a href="#" class="btn btn-icon btn-sm btn-light-primary mr-1" data-card-tool="toggle">
                <i class="ki ki-arrow-down icon-nm"></i>
            </a>
        </div>
    </div>
    <div class="card-body pt-0" style="display: none; overflow: hidden;">
        <table class="table table-borderless table-vertical-center" id="kt_advance_table_widget_1">
            <thead>
            <tr>
                <th class="p-0" style="width: 45px"></th>
                <th class="p-0" style="min-width: 150px"></th>
                <th class="pr-0 text-right" style="min-width: 100px"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($log as $item)
                <tr>
                    <td class="pl-3 w-auto">
                        @if($item->tipo == '0')
                            <div class="symbol symbol-35 symbol-light-primary">
                                <span class="symbol-label">
                                    <span class="svg-icon svg-icon-primary">
                                        {{Metronic::getSVG('media/svg/icons/Code/Info-circle.svg')}}
                                    </span>
                                </span>
                            </div>
                        @elseif($item->tipo == '1')
                            <div class="symbol symbol-35 symbol-light-success">
                                <span class="symbol-label">
                                    <span class="svg-icon svg-icon-success">
                                        {{Metronic::getSVG('media/svg/icons/Code/Plus.svg')}}
                                    </span>
                                </span>
                            </div>
                        @elseif($item->tipo == '2')
                            <div class="symbol symbol-35 symbol-light-warning">
                                <span class="symbol-label">
                                    <span class="svg-icon svg-icon-warning">
                                        {{Metronic::getSVG('media/svg/icons/Design/Edit.svg')}}
                                    </span>
                                </span>
                            </div>
                        @elseif($item->tipo == '3')
                            <div class="symbol symbol-35 symbol-light-danger">
                                <span class="symbol-label">
                                    <span class="svg-icon svg-icon-danger">
                                        {{Metronic::getSVG('media/svg/icons/Home/Trash.svg')}}
                                    </span>
                                </span>
                            </div>
                        @elseif($item->tipo == '4')
                            <div class="symbol symbol-35 symbol-light-info">
                                <span class="symbol-label">
                                    <span class="svg-icon svg-icon-info">
                                        {{Metronic::getSVG('media/svg/icons/General/Shield-protected.svg')}}
                                        {{--{{Metronic::getSVG('media/svg/icons/General/Lock.svg')}}--}}
                                    </span>
                                </span>
                            </div>
                        @elseif($item->tipo >= '5')
                            <div class="symbol symbol-35">
                                <span class="symbol-label">
                                    <span class="svg-icon">
                                        {{Metronic::getSVG('media/svg/icons/Code/Warning-2.svg')}}
                                    </span>
                                </span>
                            </div>
                        @endif
                    </td>
                    <td class="pl-0">
                        <a href="#"
                           class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">{{ $item->descricao }}</a>
                        <span
                            class="text-muted font-weight-bold d-block">{{ $item->nome }} {{ $item->sobrenome }}</span>
                    </td>
                    <td class="text-right">
                        <span
                            class="text-muted font-weight-bold d-block font-size-sm">{{ date_create($item->ocorrencia)->format('H:i:s d/m/Y') }}</span>
                        <span
                            class="text-dark-75 font-weight-bolder d-block font-size-lg">{{ $item->time_elapsed }}</span>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
