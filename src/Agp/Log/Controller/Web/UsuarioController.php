<?php
/**
 *
 * Data e hora: 2020-09-23 09:39:57
 * Controller/Web gerada automaticamente
 *
 */


namespace Agp\Log\Controller\Web;


use Agp\Log\Controller\Controller;
use Agp\Log\Log;
use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\Facades\FormBuilder;


class UsuarioController extends Controller
{
    public function usuarioAcesso(Request $request)
    {
        return view('relatorio.usuario.acesso');
    }

    public function usuarioAcessoData(Request $request)
    {
        $list = (new Log(0, '', '', auth()->user()->getAdmEmpresaId()))->getAcessosSistema();
        if (!$list) $list = [];
        foreach ($list as $item) {
//            $pessoa = new Pessoa();
//            $pessoa->email = decryptor($item->usuario->email);
//            $item->usuario->email = $pessoa->email;
//            $item->usuario->imagem = $pessoa->imagem;
        }
        return $list;
    }
}
