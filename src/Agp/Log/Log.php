<?php


namespace Agp\Log;


use Agp\BaseUtils;
use Agp\Log\Jobs\LogJob;
use App\Exception\CustomException;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Swift_Message;

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
     * @param int $tipo Tipo do registro, sendo 0 informação, 1 adição, 2 alteração, 3 remoção, 4 segurança, 5 falha, 6 erro de sistema (log em tabela Log_Erro_Sistema), 7 log de e-mail enviado.
     * @param string|Swift_Message $data Mensagem ou dump de erro ou Email enviado
     * @param string $tabela Tabela da entidade que aconteceu o log ou vazio para nenhuma.
     * @param string $empresaId ID da empresa para registra log. Se não informado utiliza empresa do usuário logado.
     */
    function __construct($tipo = 0, $data = '', $tabela = '', $empresaId = '')
    {
        $this->tipo = $tipo;
        $this->data = $data;
        $this->tabela = $tabela;
        $this->empresa = $empresaId;

        $this->init();
    }

    /**
     * Retorna o IP da requisição na hierarquia: HTTP_X_REAL_IP, HTTP_CLIENT_IP, HTTP_X_FORWARDED_FOR, request()->ip()
     */
    private function getIpRequest()
    {
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
        return request()->ip();
    }

    /** Retorna o user agent como paremtro de api ou da requisição
     * @return mixed
     */
    private function getUserAgent()
    {
        if (request()->get('client')) return request()->get('client')['user_agent']; //Se possuir client, é chamada de API
        return request()->userAgent();
    }

    /**
     * Carrega variais inicias.
     */
    private function init()
    {
        $this->token = config('log.api_client_token');
        $this->uri = config('log.api_log');
        $this->app = request()->app ? request()->app : config('log.id_app');

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
    }

    /**
     * Cria registro de acesso do usuario salvando os dados do navegador, ip, etc.
     */
    public function acesso()
    {
        $data = [
            "acesso" => date_create()->format('Y-m-d H:i:s'),
            "adm_aplicativo_id" => $this->app,
            "adm_empresa_id" => $this->empresa,
            "adm_pessoa_id" => $this->usuario,
            "user_agent" => $this->getUserAgent(),
            "ip" => $this->getIpRequest(),
        ];

        $this->send('POST', '/acesso', $data);
    }

    /**
     * Cria registro de log
     *
     * @throws Exception
     */
    public function make()
    {
        switch ($this->tipo) {
            case 6:
                $data = [
                    "adm_aplicativo_id" => $this->app,
                    "adm_empresa_id" => $this->empresa,
                    "usuario" => $this->usuario,
                    "dump" => $this->data,
                    "ocorrencia" => date_create()->format('Y-m-d H:i:s'),
                    "tabela" => $this->tabela
                ];
                $this->send('POST', '/erro-sistema/store', $data);
                break;
            case 7:
                $data = [
                    "from" => json_encode($this->data->getFrom()),
                    "to" => json_encode($this->data->getTo()),
                    "subject" => $this->data->getSubject(),
                    "body" => $this->data->getBody(),
                    "ocorrencia" => date_create()->format('Y-m-d H:i:s'),
                ];
                $this->send('POST', '/email/store', $data);
                break;
            default:
                $data = [
                    "adm_aplicativo_id" => $this->app,
                    "adm_empresa_id" => $this->empresa,
                    "usuario" => $this->usuario,
                    "descricao" => $this->data,
                    "tipo" => $this->tipo,
                    "ocorrencia" => date_create()->format('Y-m-d H:i:s'),
                    "tabela" => $this->tabela
                ];
                $this->send('POST', '/store', $data)->json();
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
        $res = $this->send('GET', '/tabela', $data);
        if (($res->status() >= 200) && ($res->status() <= 299))
            return $res->object();
        return null;
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
        $res = $this->send('GET', '/acesso/' . auth()->user()->getKey(), []);
        if (($res->status() >= 200) && ($res->status() <= 299))
            return $res->object();
        return null;
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
        $res = $this->send('GET', '/acesso', $data);
        if (($res->status() >= 200) && ($res->status() <= 299))
            return $res->object();
        return null;
    }

    /**
     * Salva log de erro inesperado. Utilizado em App\Exceptions\Handle.php
     *
     * @param $exception
     */
    public static function handleException($exception)
    {
        if (($exception instanceof \Ignition\Exceptions\ViewException) || ($exception instanceof \Facade\Ignition\Exceptions\ViewException))
            if ($exception->getPrevious())
                $exception = $exception->getPrevious();
        $arr = array();
        $arr['url'] = url()->current();
        $arr['previous_url'] = url()->previous();
        $arr['code'] = method_exists($exception, 'getCode') ? $exception->getCode() : 'No method';
        $arr['message'] = method_exists($exception, 'getMessage') ? $exception->getMessage() : 'No method';
        $arr['request'] = request()->all();
        $arr['errors'] = method_exists($exception, 'errors') ? $exception->errors() : 'No method';
        $arr['exception'] = get_class($exception);
        $arr['trace'] = method_exists($exception, 'getTrace') ? $exception->getTrace() : 'No method';
        LogJob::dispatch(new \Agp\Log\Log(6, json_encode($arr, 0, 1024)));
    }
}
