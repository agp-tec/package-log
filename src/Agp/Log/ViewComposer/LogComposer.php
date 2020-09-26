<?php

namespace Agp\Log\ViewComposer;

use Agp\Log\Log;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Retorna componentes de views relacionado a logs
 *
 * Class LogComposer
 * @package App\ViewComposer
 */
class LogComposer
{
    /**
     * Retorna array de registros de alterações da $table
     *
     * @param string $table Tabela cujo ações aconteceram
     * @return Application|Factory|View
     */
    public static function get($table)
    {
        $log = [];
        if (auth()->check() && method_exists(auth()->user(), 'getADmEmpresaId'))
            $log = (new Log(0, '', $table, auth()->user()->getAdmEmpresaId()))->get(0, 10);
        if (!$log) $log = [];
        return view('historic', ['log' => $log]);
    }

    public static function getDatatableUsuarioAcesso($id = 'r-usuario-acesso')
    {
        $datatable = new Datatable($id, false);
        $datatable->data->serverPaging = false;
        $datatable->data->serverFiltering = false;
        $datatable->data->serverSorting = false;
        RelatorioComposer::setColumnAcessoDatatable($datatable);
        RelatorioComposer::setColumnNomeDatatable($datatable);
        RelatorioComposer::setColumnRegiaoDatatable($datatable);
        RelatorioComposer::setColumnIPDatatable($datatable);
        RelatorioComposer::setColumnDeviceDatatable($datatable);
        RelatorioComposer::setColumnOSDatatable($datatable);
        RelatorioComposer::setColumnBrowserDatatable($datatable);
        $datatable->setAjaxUrl(route('web.relatorio-usuario-acesso.data'));
        return view('relatorio.usuario.acessodatatable', compact('datatable'));
    }

    private static function setColumnNomeDatatable(&$datatable)
    {
        $datatable->addColumn('nome', 'Nome')
            ->set('autoHide', false)
            ->set('sortable', false)
            ->set('width', 320);
    }

    private static function setColumnIPDatatable(Datatable $datatable)
    {
        $datatable->addColumn('ip', 'IP')
            ->set('sortable', false);
    }

    private static function setColumnAcessoDatatable(Datatable $datatable)
    {
        $datatable->addColumn('acesso', 'Data e hora')
            ->set('sortable', false);
    }

    private static function setColumnDeviceDatatable(Datatable $datatable)
    {
        $datatable->addColumn('device', 'Dispositivo')
            ->set('sortable', false);
    }

    private static function setColumnOSDatatable(Datatable $datatable)
    {
        $datatable->addColumn('os', 'Sistema operacional')
            ->set('sortable', false);
    }

    private static function setColumnBrowserDatatable(Datatable $datatable)
    {
        $datatable->addColumn('browser', 'Navegador')
            ->set('sortable', false);
    }

    private static function setColumnRegiaoDatatable(Datatable $datatable)
    {
        $datatable->addColumn('regiao', 'Localidade')
            ->set('sortable', false);
    }

}
