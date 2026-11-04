<?php

namespace App\Domain\Financeiro\Empenho\Services;

use cl_emppresta;
use cl_empprestaitem;
use cl_empprestaitemdiaria;
use cl_rhpessoal;
use db_utils;
use Exception;
use ServidorRepository;
use stdClass;

class PrestacaoDiariaService
{
    private $e60_numemp;
    private $e69_nota;
    private $valorTotal;
    private $matricula;
    private $e81_codmov;
    private $oServidor;
    private $oDadosDiaria;
    private $idUsuario;

    public function __construct()
    {
        $this->oDadosDiaria = new stdClass;
    }

    /**
     * Chama as funcoes resposáveis por
     * salvar os dados da diária
     *
     * @return void
     */
    public function save()
    {
        $this->initServidorDados();

        $iEmppresta     = $this->getEmppresta();
        $iEmpprestaItem = $this->saveEmpprestaItem($iEmppresta);

        $this->saveEmpprestaItemDiaria($iEmpprestaItem);
    }

    /**
     * Recupera sequencial da prestacao de contas
     *
     * @return int Sequencial (e45_sequencial)
     */
    private function getEmppresta()
    {
        $oDaoEmppresta = new \cl_emppresta;
        $sWhere = "e45_numemp = {$this->getEmpenho()} and e45_codmov = {$this->getMovimento()}";
        $sSqlEmppresta = $oDaoEmppresta->sql_query_emp(null, 'e45_sequencial', null, $sWhere);
        $rsEmppresta   = db_query($sSqlEmppresta);

        if (!$rsEmppresta) {
            throw new Exception("Não foi possível localizar prestação para o movimento {$this->getMovimento()}");
        }

        $iEmppresta = db_utils::fieldsMemory($rsEmppresta, 0)->e45_sequencial;
        return $iEmppresta;
    }

    /**
     * Salva dados do item
     *
     * @param int $iEmppresta (e45_sequencial)
     * @return int (e46_codigo)
     */
    private function saveEmpprestaItem($iEmppresta)
    {
        $oDaoEmpprestaItem = new cl_empprestaitem;
        $oServidorDados    = $this->oServidor->getCgm();

        $oDaoEmpprestaItem->e46_emppresta  = $iEmppresta;
        $oDaoEmpprestaItem->e46_numemp     = $this->getEmpenho();
        $oDaoEmpprestaItem->e46_id_usuario = $this->getIdUsuario();
        $oDaoEmpprestaItem->e46_nota       = $this->getNota();
        $oDaoEmpprestaItem->e46_valor      = $this->getValorTotal();
        $oDaoEmpprestaItem->e46_nome       = $oServidorDados->getNomeCompleto();
        $oDaoEmpprestaItem->e46_cpf        = $oServidorDados->getCpf();
        $oDaoEmpprestaItem->e46_descr      = $this->oDadosDiaria->descr;

        $oDaoEmpprestaItem->incluir(null);

        if ($oDaoEmpprestaItem->erro_status == '0') {
            throw new Exception($oDaoEmpprestaItem->erro_msg);
        }

        return $oDaoEmpprestaItem->e46_codigo;
    }

    /**
     * Salva dados da diaria
     *
     * @param int $iEmpprestaItem (e46_codigo)
     * @return void
     */
    private function saveEmpprestaItemDiaria($iEmpprestaItem)
    {
        $oDaoEmprestaItemDiaria = new cl_empprestaitemdiaria;
        $oDiaria = $this->getDadosDiarias();

        $qtdeDiaria = $oDiaria->qtde > 0 ? $oDiaria->qtde : 1;
        $estadoDestino = strlen($oDiaria->estadoDestino) > 0 ? $oDiaria->estadoDestino : '';
        $paisDestino = strlen($oDiaria->paisDestino) > 0 ? $oDiaria->paisDestino : '';

        $oDaoEmprestaItemDiaria->e446_empprestaitem = $iEmpprestaItem;
        $oDaoEmprestaItemDiaria->e446_regist        = $this->getMatricula();
        $oDaoEmprestaItemDiaria->e446_datainicio    = $oDiaria->saida;
        $oDaoEmprestaItemDiaria->e446_datafim       = $oDiaria->retorno;
        $oDaoEmprestaItemDiaria->e446_motivo        = $oDiaria->descr;
        $oDaoEmprestaItemDiaria->e446_destino       = $oDiaria->destino;
        $oDaoEmprestaItemDiaria->e446_quantidade    = $qtdeDiaria;
        $oDaoEmprestaItemDiaria->e446_movimento     = $this->getMovimento();
        $oDaoEmprestaItemDiaria->e446_tipodiaria    = $oDiaria->tipo;
        $oDaoEmprestaItemDiaria->e446_estadodestino = $estadoDestino;
        $oDaoEmprestaItemDiaria->e446_paisdestino   = $paisDestino;

        $oDaoEmprestaItemDiaria->incluir(null);

        if ($oDaoEmprestaItemDiaria->erro_status == '0') {
            throw new Exception($oDaoEmprestaItemDiaria->erro_msg);
        }
    }

    /**
     * Seta a model servidor para a metricula
     * informada
     *
     * @return void
     */
    private function initServidorDados()
    {
        $regist      = $this->getMatricula();
        $dadosInstituicaoServidor = $this->getInstituicaoServidor();
        $competencia = explode("-", $dadosInstituicaoServidor->competencia);
        $instituicao = $dadosInstituicaoServidor->instituicao;
        $ano         = $competencia[0];
        $mes         = $competencia[1];
        
        $oServidor = ServidorRepository::getInstanciaByCodigo($regist, $ano, $mes, $instituicao, false);

        if (!$oServidor) {
            throw new Exception("Matricula: {$regist} não encontrada");
        }

        $this->oServidor = $oServidor;
    }

    private function getInstituicaoServidor()
    {
        $dao = new cl_rhpessoal();
        $sql = $dao->sql_query_file(
            $this->getMatricula(),
            "rh01_instit as instituicao,
            (
                select
                    r11_anousu || '-' || r11_mesusu
                from
                    cfpess
                where
                    rh01_instit = r11_instit
                order by
                    (r11_anousu ||''||r11_mesusu) desc limit 1
            ) as competencia
            "
        );

        $rsSql = db_query($sql);
        
        if (!$rsSql) {
            throw new Exception("Erro ao buscar instituição de origem do servidor");
        }

        if (pg_num_rows($rsSql) == 0) {
            throw new Exception("Não encontrado a instituição de origem do servidor");
        }

        return pg_fetch_object($rsSql, 0);
    }
    /**
     * Setters
     */

    public function setEmpenho($numemp)
    {
        $this->e60_numemp = $numemp;
    }

    public function setNota($nota)
    {
        $this->e69_nota = $nota;
    }

    public function setMovimento($codmov)
    {
        $this->e81_codmov = $codmov;
    }

    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;
    }

    public function setMatricula($regist)
    {
        $this->matricula = $regist;
    }

    public function setDiariaSaida($dtSaida)
    {
        $this->oDadosDiaria->saida = $dtSaida;
    }

    public function setDiariaRetorno($dtRetorno)
    {
        $this->oDadosDiaria->retorno = $dtRetorno;
    }

    public function setDiariaDestino($dest)
    {
        $dest = mb_convert_encoding($dest, 'ISO-8859-1', 'UTF-8');
        $this->oDadosDiaria->destino = $dest;
    }

    public function setDiariaTipo($tipo)
    {
        $this->oDadosDiaria->tipo = $tipo;
    }

    public function setDiariaDescr($descr)
    {
        $descr = mb_convert_encoding($descr, 'ISO-8859-1', 'UTF-8');
        $this->oDadosDiaria->descr = $descr;
    }

    public function setDiariaQtde($qtde)
    {
        $this->oDadosDiaria->qtde = $qtde;
    }

    public function setDiariaEstadoDestino($estadoDest)
    {
        $estadoDest = mb_convert_encoding($estadoDest, 'ISO-8859-1', 'UTF-8');
        $this->oDadosDiaria->estadoDestino = $estadoDest;
    }

    public function setDiariaPaisDestino($paisDest)
    {
        $paisDest = mb_convert_encoding($paisDest, 'ISO-8859-1', 'UTF-8');
        $this->oDadosDiaria->paisDestino = $paisDest;
    }

    public function setIdUsuario($id)
    {
        $this->idUsuario = $id;
    }

    /**
     * Getters
     */

    public function getEmpenho()
    {
        return $this->e60_numemp;
    }

    public function getNota()
    {
        return $this->e69_nota;
    }

    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    public function getMatricula()
    {
        return $this->matricula;
    }

    public function getMovimento()
    {
        return $this->e81_codmov;
    }

    public function getDadosDiarias()
    {
        return $this->oDadosDiaria;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }
}
