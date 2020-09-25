<?php


namespace Agp\Log;


use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Class Log
 * @package Agp\Modelo
 */
class Log
{
    /**
     * @var int
     */
    private $tipo = 0;
    /**
     * @var string
     */
    private $data = '';
    /**
     * @var string
     */
    private $tabela = '';
    /**
     * @var string
     */
    private $empresa = '';
    /**
     * @var string
     */
    private $token = '';
    /**
     * @var string
     */
    private $uri = '';
    /**
     * @var string
     */
    private $app = '';
    /**
     * @var array
     */
    private $headers = '';
    /**
     * @var string
     */
    private $usuario;

    /**
     * Log constructor.
     * @param int $tipo Tipo do registro, sendo 0 informação, 1 adição, 2 alteração, 3 remoção, 4 segurança, 5 falha, 6 erro de sistema (log em tabela Log_Erro_Sistema).
     * @param string $data Mensagem ou dump de erro.
     * @param string $tabela Tabela da entidade que aconteceu o log ou vazio para nenhuma.
     * @param string $empresaId ID da empresa para registra log. Se não informado utiliza empresa do usuário logado.
     */
    function __construct($tipo = 0, $data = '', $tabela = '', $empresaId = '')
    {
        $this->init();

        $this->tipo = $tipo;
        $this->data = $data;
        $this->tabela = $tabela;
        $this->empresa = $empresaId;
    }

    /**
     * Carrega variais inicias.
     */
    private function init()
    {
        $this->token = config('log.api_client_token');
        $this->uri = config('log.api_log');
        $this->app = config('log.id_app');

        $this->headers = [
            'Content-type' => 'application/json',
            'Accept' => 'application/json',
            'client-token' => $this->token,
        ];

        $this->usuario = null;
        if (auth()->check()) {
            $this->usuario = auth()->user()->getKey();
            if (method_exists(auth()->user(), 'getAdmEmpresaId') && ($this->empresa == ''))
                $this->empresa = auth()->user()->getAdmEmpresaId();
        }
        if ($this->tabela == '')
            $this->tabela = 'Sistema';

    }

    /** Realiza comunicação com API
     * @param string $method Metodo HTTP da requisição (GET, POST, PUT, DELETE)
     * @param string $patch Caminho da rota
     * @param array $body
     * @return Response
     * @throws Exception
     */
    private function send($method, $patch, $body)
    {
        $this->verify();
        if ($method == 'POST')
            return Http::withHeaders($this->headers)->post($this->uri . $patch, $body);
        elseif ($method == 'GET')
            return Http::withHeaders($this->headers)->get($this->uri . $patch, $body);
        elseif ($method == 'PUT')
            return Http::withHeaders($this->headers)->put($this->uri . $patch, $body);
        elseif ($method == 'DELETE')
            return Http::withHeaders($this->headers)->delete($this->uri . $patch, $body);
        throw new Exception('METODO ' . $method . ' não permitido.');
    }

    /** Verifica se possui parâmetros necessários para realizar requisição
     * @throws Exception
     */
    private function verify()
    {
        if ($this->uri == '')
            throw new Exception('URI não informado');
        if ($this->app == '')
            throw new Exception('APP não informado');
        if ($this->token == '')
            throw new Exception('TOKEN não informado');
        if ($this->data == '')
            throw new Exception('URI não informado');
    }

    /**
     * Cria registro de log
     *
     * @throws Exception
     */
    public function make()
    {
        if ($this->tipo > 5) {
            $data = [
                "adm_aplicativo_id" => $this->app,
                "adm_empresa_id" => $this->empresa,
                "usuario" => $this->usuario,
                "dump" => $this->data,
                "ocorrencia" => date_create()->format('Y-m-d H:i:s'),
                "tabela" => $this->tabela
            ];
            $this->send('POST', '/erro-sistema/store', $data);
        } else {
            $data = [
                "adm_aplicativo_id" => $this->app,
                "adm_empresa_id" => $this->empresa,
                "usuario" => $this->usuario,
                "descricao" => $this->data,
                "tipo" => $this->tipo,
                "ocorrencia" => date_create()->format('Y-m-d H:i:s'),
                "tabela" => $this->tabela
            ];
            $this->send('POST', '/store', $data);
        }
    }

    /**
     * Retorna registros de log
     *
     * @param int $start Inicio dos registros consideranco ordenação do mais novo para o mais antigo
     * @param int $count Quantidade máxima de registros retornados
     * @return array|null
     * @throws Exception
     */
    public function get($start = 0, $count = 10)
    {
        if ($this->tabela == '')
            throw new Exception('TABELA não informado');
        if ($this->empresa == '')
            throw new Exception('EMPRESA não informado');

        $data = [
            "app" => $this->app,
            "start" => $start,
            "count" => $count,
            "tabela" => $this->tabela,
            "empresa" => $this->empresa,
        ];

        return $this->send('/tabela', $data)->json();
    }

    /**
     * Retorna os acessos do usuario logado
     *
     * @return array|null
     * @throws Exception
     */
    public function getAcessos()
    {
        if (!auth()->check() || !auth()->user())
            throw new Exception('USUARIO não logado');
        return $this->send('acesso/' . auth()->user()->getKey(), [])->json();
    }

    /**
     * Retorna registros os acessos ao sistema da empresa
     *
     * @return array|null
     * @throws Exception
     */
    public function getAcessosSistema()
    {
        if ($this->empresa == '')
            throw new Exception('EMPRESA não informado');

        $data = [
            "adm_aplicativo_id" => $this->app,
            "adm_empresa_id" => $this->empresa,
        ];

        return $this->send('/acesso', $data)->json();
    }
}
