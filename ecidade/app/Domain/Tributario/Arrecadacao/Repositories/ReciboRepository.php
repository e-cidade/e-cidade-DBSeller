<?php

namespace App\Domain\Tributario\Arrecadacao\Repositories;

use App\Domain\Tributario\Arrecadacao\Models\Arretipo;
use App\Domain\Tributario\Arrecadacao\Services\ArrecadacaoPixService;
use App\Domain\Tributario\Caixa\Models\Arrematric;
use Illuminate\Support\Facades\DB;

use ECidade\V3\Extension\Registry;
use ECidade\V3\Extension\Request;

use ECidade\Tributario\Arrecadacao\Custas\Service\Recibo as ReciboCustasService;
use ECidade\Tributario\Arrecadacao\Custas\Enum\TipoModelo as TipoModeloCustas;

use Exception;

class ReciboRepository
{
    const QUEUE_NAME = 'emissao_pix_api';
    const QUEUE_CREATE_NAME = 'create_emissao_pix_api';

    /**
     * Mostrar logs
     *
     * @var bool $viewLogs
     */
    public static $viewLogs = true;

    /**
     * Usar fake request
     *
     * @var bool $useFakeRequest
     */
    public static $useFakeRequest = true;

    /**
     * Valor da taxa bancaria
     *
     * @var int|float $taxabancaria Valor da taxa bancaria, default 0
     */
    private static $taxabancaria = 0;

    /**
     * Pegar Matricula e CGM
     *
     * @param int $k00_numpre
     *
     * @throws Exception Resultado
     *
     * @return object
     */
    public static function getMatriculaNumCGM($k00_numpre)
    {
        $sql =<<<SQL
            SELECT 
                *
            FROM
                caixa.arrematric a
            INNER JOIN
                cadastro.iptubase i ON i.j01_matric = a.k00_matric
            WHERE
                a.k00_numpre = {$k00_numpre};
SQL;

        $result = DB::select(DB::raw($sql));

        if (count($result) !== 1) {
            throw new Exception('Nenhum ou mais de um resultado foi encontrado');
        }

        return $result[0];
    }

    /**
     * Cria o código de barras
     *
     * @param object $valores Valor calculado do recibo
     *
     * @return void
     */
    public static function getCodigoBarra(&$valores)
    {
        self::logJob('Gerando Codigo de Barra');

        $valores->vlrbar = self::formataBar($valores->recibo->getTotalRecibo());

        self::logJob('FIM Codigo de Barra');
    }

    public static function getDescontoNitNota($k00_numpre, $anoiptu)
    {
        $result    = 0;
        $matricula = null;
    
        $matricula = Arrematric::where('k00_numpre', $k00_numpre)
            ->select('k00_matric')
            ->first()
            ->k00_matric;

        $sql =<<<SQL
            select
                valor as valornota
            from
                plugins.iptudescontonfse
            where
                matricula = {$matricula} and
                exercicio = {$anoiptu};
SQL;

        $nitNota = DB::select($sql);

        if (isset($nitNota) and count($nitNota) > 0) {
            $nitNota = $nitNota[0]->valornota;

            if (is_numeric($nitNota)) {
                $result = (float) $nitNota;
            }
        }

        return $result;
    }

    /**
     * Retorno o linha da tabela arretipo
     *
     * @param int $tipoDebito k00_tipo
     *
     * @return Arretipo
     */
    public static function getArretipo($tipoDebito)
    {
        return Arretipo::getArretipoByTipoDebito($tipoDebito);
    }

    /**
     * Rotina do job para gerar o recibo o codigo de barra para
     * lançar a emissão do QRcode do pix
     *
     * @param object $valores Lista de valores para emissão
     * @param Datatime Data do lançamento da emissão
     * @param int $instit  sequencial da instituição que fez a emissão
     *
     * @throws Exception
     *
     * @return void
     */
    public static function gerarDadosEmissao(
        &$valores,
        $datause,
        $instit,
        $k00_numpra = 0
    ) {
        self::fakeRequest($datause, $instit);
        self::gerarRegraEmissao($valores);
        self::gerarRecibo($valores, $k00_numpra);
        self::getCodigoBarra($valores);
        self::gerarConvenio($valores, $k00_numpra);
        
        if ($k00_numpra == 0) {
            self::gerarQRPix($valores);
        }
    }

    /**
     * Gera Recibo para o job de emissão
     *
     * @param object $valores Lista de valores para emissão
     *
     * @return void
     */
    private static function gerarRecibo(&$valores, &$k00_numpar)
    {
        self::logJob('Gerando Recibo');

        require_once(modification(ECIDADE_PATH . "model/recibo.model.php"));
        
        $valores->recibo = new \recibo(2, null, 1);
        
        $valores->recibo->setNumBco(
            $valores->oRegraEmissao->getCodConvenioCobranca()
        );

        $valores->recibo->setDataVencimentoRecibo(
            $valores->dataVencimento
        );

        // $valores->recibo->setDescontoReciboWeb(
        //     $valores->k00_numpre,
        //     $k00_numpar,
        //     $valores->cotaUnica->valorDesconto
        // );

        $valores->recibo->addNumpre($valores->k00_numpre, $k00_numpar);
        $valores->recibo->emiteRecibo(false, true, null, true);

        self::logJob('Emitindo Recibo');
        
        if ($k00_numpar != 0) {
            $sql =<<<SQL
                SELECT
                    k00_valor
                FROM
                    caixa.recibopaga
                WHERE
                    k00_numnov = {$valores->recibo->getNumpreRecibo()}
                    and k00_numpar = {$k00_numpar}
SQL;
            
            $result = db_query($sql);
            $result = pg_fetch_all($result);
            $total  = 0;

            foreach ($result as $recibopaga) {
                $total += $recibopaga['k00_valor'];
            }

            if ($total < 0) {
                throw new Exception(
                    "O valor da parcela {$k00_numpar} esta menor que 0"
                );
            }

            $valores->utotal = $total;
        }

        self::logJob('FIM Recibo');
    }

    /**
     * Gera Regra de emissão para o job de Emissão
     *
     * @param object $valores Lista de valores para emissão
     *
     * @return void
     */
    private static function gerarRegraEmissao(&$valores)
    {
        self::logJob('Gerando Regra Emissão');

        require_once(modification(ECIDADE_PATH . "model/regraEmissao.model.php"));

        $valores->oRegraEmissao = new \regraEmissao(
            $valores->arretipo['k00_tipo'],
            1,
            db_getsession('DB_instit'),
            date("Y-m-d", db_getsession("DB_datausu")),
            db_getsession('DB_ip'),
            true,
            false,
            1,
            1,
            false
        );

        self::logJob('FIM Gerando Regra Emissão');
    }
    
    /**
     * Gera Convenio para o job de emissão
     *
     * @param object $valores Lista de valores para emissão
     *
     * @return void
     */
    private static function gerarConvenio(&$valores, &$k00_numpra)
    {
        self::logJob('Gerando Convenio');

        require_once(modification(ECIDADE_PATH . "model/convenio.model.php"));

        $valores->oConvenio = new \convenio(
            $valores->oRegraEmissao->getConvenio(),
            $valores->recibo->getNumpreRecibo(),
            0,
            $valores->recibo->getTotalRecibo(),
            $valores->vlrbar,
            $valores->dataVencimento,
            $valores->arretipo['k00_tercdigcarneunica']
        );

        $valores->codigo_barras = $valores->oConvenio->getCodigoBarra();

        self::logJob('FIM Convenio');
    }

    /**
     * Gera Recibo Custas para o jbo de emissão
     *
     * @param object $valores Lista de valores para emissão
     *
     * @return void
     */
    private static function gerarReciboCustasService(&$valores)
    {
        self::logJob('Gerando ReciboCustaService');
     
        $valores->iTipoMod = 1;

        $valores->reciboCustasService = new ReciboCustasService(
            $valores->arretipo['k03_tipo']
        );

        if ($valores->reciboCustasService->validaUsoDeCustas()) {
            $valores->iTipoMod = TipoModeloCustas::CARNE;
        }

        self::logJob('FIM ReciboCustaService');
    }

    /**
     * Rotina do job gerar QRcode nos bancos selecionandos
     *
     * @param object $valores
     *
     * @throws Exception
     *
     * @return void
     */
    private static function gerarQRPix(&$valores)
    {
        self::logJob('Gerar QRcode PIX');

        $valores->proprietario = self::getMatriculaNumCGM(
            $valores->k00_numpre
        );

        $result = ArrecadacaoPixService::validaEmissaoPix(
            $valores->arretipo['k00_tipo'],
            $valores->oRegraEmissao,
            $valores->recibo->getNumpreRecibo(),
            true
        );

        if ($result) {
            $valores->emissaoPix = new ArrecadacaoPixService();
            
            $valores->emissaoPix->setTipoDebito($valores->arretipo['k00_tipo']);
            $valores->emissaoPix->setVencimento(
                $valores->dataVencimento
            );

            $valores->emissaoPix->geraPixCarne(
                $valores->oConvenio,
                $valores->recibo->getNumpreRecibo(),
                $valores->proprietario->j01_numcgm,
                $valores->recibo->getTotalRecibo()
            );
        } else {
            throw new Exception(
                'Ocorreu um erro na tentativa de validação a emissão PIX.'
            );
        }

        self::logJob('FIM QRcode');
    }

    /**
     * Cria uma fake request para se possivel usar a dao antigas
     *
     * @return void
     */
    private static function fakeRequest($datause, $instit)
    {
        self::logJob('Carregando FakeRequest');

        if (self::$useFakeRequest) {
            if (session_status() === PHP_SESSION_DISABLED) {
                session_start();
            }
    
            $fakeRequest = new Request();
            
            Registry::set('app.request', $fakeRequest);
    
            $GLOBALS['CONNECTION']            = true;
            
            $_SESSION["DB_acessado"]          = 2000287;
            $_SESSION["DB_datausu"]           = $datause->getTimestamp();
            $_SESSION['DB_anousu']            = $datause->format('Y');
            $_SESSION["DB_login"]             = "dbseller";
            $_SESSION["DB_id_usuario"]        = "1";
            $_SESSION["DB_coddepto"]          = 1;
            $_SESSION['DB_instit']            = $instit;
            $_SESSION['DB_ip']                = '127.0.0.1';
            $_SESSION['DB_uol_hora']          = $datause->getTimestamp();
            $_SESSION['DB_nome_modulo']       = 'Configuração';
            $_SESSION['DB_modulo']            = 1;
            $_SESSION['DB_id_usuario']        = 1;
            $_SESSION['DB_desativar_account'] = true;
    
            $_SERVER['REQUEST_URI'] = 'localhost';
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            
            $HTTP_SERVER_VARS["REMOTE_ADDR"] = '127.0.0.1';
            $HTTP_SERVER_VARS["HTTP_HOST"]   = '127.0.0.1';
            $HTTP_SERVER_VARS["PHP_SELF"]    = '/';
            
            require_once(modification(ECIDADE_PATH . "libs/db_stdlib.php"));
            require_once(modification(ECIDADE_PATH . "libs/db_utils.php"));
            require_once(modification(ECIDADE_PATH . "libs/db_conecta.php"));
        }

        self::logJob('FIM FakeRequest');
    }

    /**
     * Função de dar feedback na rotina de job
     *
     * @param string $msg Feedback
     *
     * @return void
     */
    private static function logJob($msg = '')
    {
        if (self::$viewLogs) {
            echo mb_convert_encoding('[' . date('Y-m-d H:i:s') . '] ' . $msg, 'UTF-8', 'UTF-8'), PHP_EOL;
        }
    }

    /**
     * Formatar o valor igual a função db_formart f
     *
     * @param mixed $valor
     *
     * @return string
     */
    private static function formata($valor)
    {
        return str_pad(
            number_format($valor, 2, ",", "."),
            15,
            " ",
            STR_PAD_LEFT
        );
    }

     /**
     * Formatar o valor igual a função db_formart e
     *
     * @param mixed $valor
     *
     * @return string
     */
    private static function formataBar($valor)
    {
        $valor = str_replace(
            '.',
            '',
            str_pad(
                number_format($valor, 2, "", "."),
                11,
                "0",
                STR_PAD_LEFT
            )
        );
        
        return str_pad(
            $valor,
            11,
            '0',
            STR_PAD_LEFT
        );
    }
}
