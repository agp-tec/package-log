<?php

Route::group(['as' => 'web.', 'namespace' => 'Agp\Modelo\Controller\Web', 'middleware' => ['web']], function()
{
    Route::resource('cidade', 'CidadeController');
});

Route::get('home', function () {
    return view('Log::cidade/index');
});

?>
