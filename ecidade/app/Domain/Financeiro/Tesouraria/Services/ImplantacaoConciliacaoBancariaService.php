<?php


namespace App\Domain\Financeiro\Tesouraria\Services;

use App\Domain\Configuracao\Usuario\Models\Usuario;
use db_utils;
use DBDate;
use Exception;
use stdClass;

/**
 * Class ImplantacaoConciliacaoBancariaService
 * @package App\Domain\Financeiro\Tesouraria\Services
 */
class ImplantacaoConciliacaoBancariaService
{
    //private $repository;

    private $instituicao;
    private $ano;
    private $usuario;

    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
    }
    public function getInstituicao()
    {
        return $this->instituicao;
    }

    public function setAno($ano)
    {
        $this->ano = $ano;
    }
    public function getAno()
    {
        return $this->ano;
    }

    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }
    public function getUsuario()
    {
        return $this->usuario;
    }



    public function getDatasContasConciliar($iAno, $iInstituicao, $conta, $reduzidos)
    {

        $oDaoSaltes   = new \cl_saltes;
        $sqlDatas = $oDaoSaltes->sql_query_DataContas($iAno, $iInstituicao, $conta, $reduzidos);
        $rsDatas = $oDaoSaltes->sql_record($sqlDatas);

        $aDatas = array();
        for ($i = 0; $i < $oDaoSaltes->numrows; $i++) {
            $oDatas = db_utils::fieldsMemory($rsDatas, $i);
            $data =  db_formatar($oDatas->k12_data, "d");
            $aDatas[] = $data;
        }
         return $aDatas;
    }


    public function getRegistrosImplantar()
    {

        $oDaoSaltes = new \cl_saltes;
        $iAno = $this->getAno();
        $iInstit = $this->getInstituicao();

        $sqlConta = $oDaoSaltes->sql_query_contasImplantar($iAno, $iInstit);
       
        $rsContas    = $oDaoSaltes->sql_record($sqlConta);
        $numrows     = $oDaoSaltes->numrows;
        $dados = array();

        for ($i = 0; $i < $numrows; $i++) {
            $oDados = db_utils::fieldsMemory($rsContas, $i);

            $oLinha = new stdClass();
            $oLinha->checkbox = false;
            $oLinha->conta = $oDados->db83_sequencial;
            $oLinha->descricao = $oDados->db83_descricao;
            $oLinha->reduzido = $oDados->c61_reduz;
            $oLinha->dataImplantar=$this->getDatasContasConciliar($iAno, $iInstit, $oLinha->conta, $oDados->c61_reduz);
            $dados[] = $oLinha;
        }
        return  $dados;
    }

    public function processarImplantacao($dados)
    {

        $iInstituicao = $this->getInstituicao();
        $iAno = $this->getAno();
        $hora = db_hora();
        $usuario = $this->getInstituicao();

        $clcorrente = new \cl_corrente;
        $clconcilia = new \cl_concilia;
        $clconciliaitem = new \cl_conciliaitem;
        $clconciliacor = new \cl_conciliacor;
        $oDaoExtratolinha = new \cl_extratolinha;
        $oDaoConciliaExtrato = new \cl_conciliaextrato;

        foreach ($dados as $oConta) {
            $data = DBDate::converter($oConta->data);
            $conta  = $oConta->conta;

            $sWhereReduz  = " select c61_reduz ";
            $sWhereReduz .= "   from contabancaria ";
            $sWhereReduz .= "        inner join conplanocontabancaria on conplanocontabancaria.c56_contabancaria = ";
            $sWhereReduz .= "                                          contabancaria.db83_sequencial ";
            $sWhereReduz .= "                                      and conplanocontabancaria.c56_anousu = {$iAno} ";
            $sWhereReduz .= "        inner join conplanoreduz  on conplanoreduz.c61_codcon = ";
            $sWhereReduz .= "                                     conplanocontabancaria.c56_codcon ";
            $sWhereReduz .= "                                 and conplanoreduz.c61_anousu = ";
            $sWhereReduz .= "                                     conplanocontabancaria.c56_anousu ";
            $sWhereReduz .= "                                 and conplanoreduz.c61_anousu = {$iAno}";
            $sWhereReduz .= "                                 and conplanoreduz.c61_instit = {$iInstituicao}";
            $sWhereReduz .= "  where contabancaria.db83_sequencial = {$conta} ";

            $campoSoma = "round(coalesce(sum(k12_valor),0),2) as totalcorrente";

            $whereSoma  = "      k12_data <= '{$data}' ";
            $whereSoma .= "  and k12_conta in ($sWhereReduz) ";

            $sqlTotal = $clcorrente->sql_query_file(null, null, null, $campoSoma, null, $whereSoma);

            $rsTotalCorrente = $clcorrente->sql_record($sqlTotal);
            $totalcorrente = db_utils::fieldsMemory($rsTotalCorrente, 0)->totalcorrente;

            $clconcilia->k68_data           = $data;
            $clconcilia->k68_contabancaria  = $conta;
            $clconcilia->k68_saldoextrato   = $totalcorrente;
            $clconcilia->k68_saldocorrente  = $totalcorrente;
            $clconcilia->k68_conciliastatus = 2;
            $clconcilia->incluir(null);

            if ($clconcilia->erro_status == "0") {
                throw new Exception("[1] - Erro ao Incluir dados da conciliacao: " . $clconcilia->erro_msg);
            }

            $clconciliaitem->k83_conciliatipo = 3;
            $clconciliaitem->k83_concilia     = $clconcilia->k68_sequencial;
            $clconciliaitem->k83_hora         = $hora;
            $clconciliaitem->k83_usuario      = $usuario;
            $clconciliaitem->incluir(null);



            if ($clconciliaitem->erro_status == "0") {
                throw new Exception("[2] - Erro ao Incluir item na conciliacao: " . $clconciliaitem->erro_msg);
            }

            $sSqlBuscaTesouraria = <<<SQL

               insert into conciliacor
               select nextval('conciliacor_k84_sequencial_seq') as sequencial,
                      '{$clconciliaitem->k83_sequencial}' as conciliaitem,
                      k12_id,
                      k12_data,
                      k12_autent,
                      1 as conciliaorigem
               from (select distinct riCaixa as k12_id,
                                     riAutent as k12_autent,
                                     riData as k12_data
                     from fc_extratocaixa({$iInstituicao}, $conta, '1500-01-01', '{$data}', false) as x
                          left join conciliacor on k84_id     = ricaixa
                                               and k84_data   = ridata
                                               and k84_autent = riautent
                     where k84_conciliaitem is null) as x

SQL;

            $rsCorrente = db_query($sSqlBuscaTesouraria);

            if (!$rsCorrente) {
                throw new Exception("[3] - Erro ao Incluir conciliacao: " . pg_last_error());
            }

            /*
            $intNumrows = pg_num_rows($rsCorrente);

            for ($i = 0; $i < $intNumrows; $i++) {
                $oStdDadosCaixa = db_utils::fieldsMemory($rsCorrente, $i);

                $clconciliacor->k84_conciliaitem   = $clconciliaitem->k83_sequencial ;
                $clconciliacor->k84_id             = $oStdDadosCaixa->k12_id;
                $clconciliacor->k84_data           = $oStdDadosCaixa->k12_data;
                $clconciliacor->k84_autent         = $oStdDadosCaixa->k12_autent;
                $clconciliacor->k84_conciliaorigem = 1;
                $clconciliacor->incluir(null);
                if ($clconciliacor->erro_status == "0") {
                    throw new Exception("[3] - Erro ao Incluir conciliacao: " . $clconciliacor->erro_msg);
                }
            }
            */
        /**
         * após incluir na conciliacor, buscamos os registros da extratolinha
         *  filtrando pela data <= e pela conta passada
         *  percorrer os registros retornados da extratolinha e incluir na conciliaextrato
         *
         * k86_data           <=  $data
         * k86_contabancaria   =  $conta
         */

            $sWhereExtratoLinha  = "k86_data <= '{$data}' and  k86_contabancaria = {$conta} ";

            $sSqlExtratoLinha   = $oDaoExtratolinha->sql_query(null, "k86_sequencial", null, $sWhereExtratoLinha);
            $rsExtratoLinha     = $oDaoExtratolinha->sql_record($sSqlExtratoLinha);
            $iTotalExtratoLinha = $oDaoExtratolinha->numrows;

            if ($iTotalExtratoLinha > 0) {
                for ($iExtratoLinha = 0; $iExtratoLinha <  $iTotalExtratoLinha; $iExtratoLinha++) {
                    $oExtratoLinha = db_utils::fieldsMemory($rsExtratoLinha, $iExtratoLinha);
                    $oDaoConciliaExtrato->k87_conciliaitem   = $clconciliaitem->k83_sequencial;
                    $oDaoConciliaExtrato->k87_extratolinha   = $oExtratoLinha->k86_sequencial;
                    $oDaoConciliaExtrato->k87_conciliaorigem = 1;
                    $oDaoConciliaExtrato->incluir(null);
                    if ($oDaoConciliaExtrato->erro_status == "0") {
                        throw new Exception("[4] - Erro ao Incluir extrato: " . $oDaoConciliaExtrato->erro_msg);
                    }
                }
            }
        }
    }
}
