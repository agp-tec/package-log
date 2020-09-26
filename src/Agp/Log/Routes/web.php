<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['as' => 'web.', 'middleware' => 'auth:web', "prefix" => "{contaId}", 'namespace' => 'Agp\Modelo\Controller\Web'], function () {
    //Rotas de relatório
    Route::get('relatorio-usuario-acesso', 'UsuarioController@usuarioAcesso')->name('relatorio-usuario-acesso');
    Route::get('relatorio-usuario-acesso-data', 'UsuarioController@usuarioAcessoData')->name('relatorio-usuario-acesso.data');
    //Route::get('relatorio-usuario-acoes','RelatorioController@usuarioAcoes')->name('relatorio-usuario-acoes');
});
