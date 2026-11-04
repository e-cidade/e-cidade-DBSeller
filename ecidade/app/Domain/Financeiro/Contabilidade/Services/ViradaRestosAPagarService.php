<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

namespace App\Domain\Financeiro\Contabilidade\Services;

use App\Domain\Configuracao\Models\DBVirada;
use App\Domain\Configuracao\Models\DBViradaItem;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoExeContaCorrente;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoExercicio;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoReduzido;
use App\Domain\Financeiro\Contabilidade\Models\ConplanoSistema;
use App\Domain\Configuracao\Models\DBViradaItemLog;
use App\Domain\Financeiro\Contabilidade\Repositories\OrigemComplementoRecursoRepository;
use App\Domain\Financeiro\Empenho\Models\Empenho;
use App\Domain\Financeiro\Empenho\Models\EmpenhoResto;
use App\Domain\Financeiro\Empenho\Models\EmpenhoRestoTipo;
use App\Domain\Financeiro\Orcamento\Models\OrigemComplementoRecurso;

use Illuminate\Support\Facades\DB;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\Estrutural;
use Exception;

class ViradaRestosAPagarService
{
    /**
     * @var integer
     */
    protected $exercicioAtual;
    /**
     * @var int
     */
    private $proximoExercicio;
    /**
     * @var integer
     */
    private $instituicao;
    /**
     * @var string
     */
    private $dataInicial;
    /**
     * @var string
     */
    private $dataFinal;
    /**
     * @var int
     */
    private $codigoUsuario;

    public function setExercicio($exercicio)
    {
        $this->exercicioAtual = $exercicio;
        $this->proximoExercicio = $exercicio + 1;
        return $this;
    }

    public function setInstituicao($instituicao)
    {
        $this->instituicao = $instituicao;
        return $this;
    }

    public function setUsuario($codigoUsuario)
    {
        $this->codigoUsuario = $codigoUsuario;
        return $this;
    }

    public function processar()
    {
        $this->dataInicial = "{$this->exercicioAtual}-01-01";
        $this->dataFinal = "{$this->exercicioAtual}-12-31";
        if ($this->validaAberturaExercicio()) {
            $msg = "A 'Abertura do Exercício' já foi processada em {$this->proximoExercicio}. Acesse: ";
            $msg .= " 'Contabilidade > Procedimentos > Escrituração Contábil > ";
            $msg .= " Abertura do Exercício > Abertura Contábil' e cancele os documentos: 2032 e 2033";
            throw new Exception($msg);
        }
        $this->processarRestos();
    }

    private function validaAberturaExercicio()
    {
        $sql = <<<SQL
            select distinct 1
                from contabilidade.conlancam
                join contabilidade.conlancamdoc on c71_codlan = conlancam.c70_codlan
            where c70_anousu = {$this->proximoExercicio}
                and c71_coddoc in (2032, 2033)
SQL;
        $dados = DB::select($sql);
        if (sizeof($dados) > 0) {
            return true;
        }
        return false;
    }

    private function processarRestos()
    {
        // Salvando dados da virada
        $virada = new DBVirada();
        $virada->c30_sequencial = DBVirada::getCodigoNewVirada();
        $virada->c30_anoorigem = $this->exercicioAtual;
        $virada->c30_anodestino = $this->proximoExercicio;
        $virada->c30_usuario = $this->codigoUsuario;
        $virada->c30_data = date("Y-m-d");
        $virada->c30_hora = date("H:i");
        $virada->c30_situacao = 1;
        $virada->save();
        // Salvando dados do item da virada
        $itemVirada = new DBViradaItem();
        $itemVirada->c31_sequencial = DBViradaItem::getCodigoNewViradaItem();
        $itemVirada->c31_db_virada = $virada->c30_sequencial;
        $itemVirada->c31_db_viradacaditem = 13; // restos a pagar
        $itemVirada->c31_situacao = 1;
        $itemVirada->save();
        $anoBusca = $this->exercicioAtual;
        if (file_exists("config/pcasp.txt")) {
            $aPcasp = file("config/pcasp.txt");
            if ($aPcasp[0] == 2013) {
                $anoBusca = $this->proximoExercicio;
            }
        }
        $tiposRps = EmpenhoRestoTipo::all();

        // RESTOS A PAGAR
        // Deletamos todos os emprestos do proximo exercicio (No caso de estar reprocessando a virada)
        EmpenhoResto::where('e91_anousu', $this->proximoExercicio)->delete();
        // Empenhos candidatos a irem para o proximo ano
        $empenhosOrigem = EmpenhoResto::where('e91_anousu', $this->exercicioAtual)->get();
        // Não pode retornar nada!!!
        $empenhosDestino = EmpenhoResto::where('e91_anousu', $this->proximoExercicio)->get();
        $processa = true;
        if (sizeof($empenhosOrigem) > 0 && sizeof($empenhosDestino) == 0) {
            $empenhos = $this->buscaEmpenhosVirada();
            foreach ($empenhos as $empenho) {
                $tipo = $this->getCodigoTipo($tiposRps, $empenho->c60_estrut);
                $resto = new EmpenhoResto();
                $resto->e91_anousu = $this->proximoExercicio;
                $resto->e91_numemp = $empenho->e60_numemp;
                $resto->e91_vlremp = $empenho->e60_vlremp;
                $resto->e91_vlranu = $empenho->e60_vlranu;
                $resto->e91_vlrliq = $empenho->e60_vlrliq;
                $resto->e91_vlrpag = $empenho->e60_vlrpag;
                $resto->e91_elemento = $empenho->c60_estrut;
                $resto->e91_recurso = $empenho->o58_codigo;
                $resto->e91_codtipo = $tipo;
                $resto->e91_rpcorreto = "false";
                $resto->save();

                if ($tipo == 999) {
                    $mensagem = "Empenho [{$empenho->e60_numemp}] sem tipo para Desdobramento: "
                        . "[{$empenho->c60_estrut}] processado utilizando o tipo 999";
                    $viradaItemLog = new DBViradaItemLog();
                    $viradaItemLog->c35_codarq = 1011; //codigo da tabela empresto
                    $viradaItemLog->c35_db_viradaitem = $itemVirada->c31_sequencial;
                    $viradaItemLog->c35_data = date("Y-m-d");
                    $viradaItemLog->c35_hora = date("H:i");
                    $viradaItemLog->c35_log = $mensagem;
                    $viradaItemLog->save();
                    $processa = false;
                    break;
                }
            }
            if ($processa) {
                $restosAnteriores = EmpenhoResto::where('e91_anousu', $this->exercicioAtual)->get();
                foreach ($restosAnteriores as $resto) {
                    $this->processaRestoAnterior($resto);
                }

                $this->processsaOrigemRestos();
            }
        } else {
            $mensagem = '';
            if (sizeof($empenhosOrigem) == 0) {
                $mensagem = "Não existem dados (empresto) para o exercicio {$this->exercicioAtual}.\r\n";
            }
            if (sizeof($empenhosDestino) == 0) {
                $mensagem .= "Ja existem dados  (empresto) para ano de destino {$this->proximoExercicio}.";
            }
            $viradaItemLog = new DBViradaItemLog();
            $viradaItemLog->codarq = 1011; //codigo da tabela empresto
            $viradaItemLog->c35_db_viradaitem = 1011;
            $viradaItemLog->c35_data = date("Y-m-d");
            $viradaItemLog->c35_hora = date("H:i");
            $viradaItemLog->c35_log = $mensagem;
            $viradaItemLog->save();
            return false;
        }
        return true;
    }

    public function verificaProcessamento()
    {
        $sql = <<<SQL
            select
                *
            from
                configuracoes.db_virada
                inner join configuracoes.db_viradaitem on c31_db_virada = c30_sequencial
            where
                c31_db_viradacaditem = 13
                and (
                    c30_anoorigem = {$this->exercicioAtual}
                )
SQL;
        $dados = DB::select($sql);

        if (sizeof($dados) > 0) {
            return true;
        }

        return false;
    }

    protected function buscaEmpenhosVirada()
    {
        $sql = <<<SQL
            select distinct on (e60_numemp)
                e60_numemp,
                e64_codele,
                c60_estrut,
                round(vlremp,2)::float8 as e60_vlremp,
                round(coalesce(vlranu,0),2)::float8 as e60_vlranu,
                round(coalesce(vlrliq,0),2)::float8 as e60_vlrliq,
                round(coalesce(vlrpag,0),2)::float8 as e60_vlrpag,
                (
                    select
                        o206_recurso
                    from
                        origemcomplementorecurso
                    where
                        o206_numero = e60_numemp
                        and o206_origem = 1
                ) as o58_codigo,
                e60_instit
            from
                empempenho
                inner join orcdotacao on o58_anousu = e60_anousu and o58_coddot = e60_coddot
                inner join empelemento on e60_numemp = e64_numemp
                inner join conplanoorcamento on c60_codcon = e64_codele and c60_anousu = e60_anousu
                inner join (
                    select
                        c75_numemp,
                        round(sum(case when c53_tipo in (10) then round(c70_valor, 2) end), 2) as vlremp,
                        round(sum(case when c53_tipo in (11) then round(c70_valor, 2) end), 2) as vlranu,
                        round(sum(case when c53_tipo in (20) then round(c70_valor, 2)
                        when c53_tipo in (21) then round(c70_valor, 2) * -1 end), 2) as vlrliq,
                        round(sum(
                            case when c53_tipo in (30) then round(c70_valor, 2)
                            when c53_tipo in (31) then round(c70_valor, 2) * -1 end ), 2
                        ) as vlrpag
                    from
                        conlancamemp
                        inner join conlancam on c70_codlan = c75_codlan
                        inner join conlancamdoc on c70_codlan = c71_codlan
                        inner join conhistdoc on c53_coddoc = c71_coddoc
                    where
                        c53_tipo in (10, 11,20, 21, 30, 31)
                        and c70_anousu = {$this->exercicioAtual}
                        and c71_data < '{$this->proximoExercicio}-01-01'
                    group by
                        c75_numemp
                ) as vlremp on vlremp.c75_numemp = e60_numemp
            where
                round(
                    round(round(vlremp,2) - round(coalesce(vlranu, 0), 2), 2)::float8
                    - round(coalesce(vlrpag, 0), 2)::float8, 2
                ) > 0
                and e60_anousu = {$this->exercicioAtual}
SQL;
        $dados = DB::select($sql);
        return $dados;
    }

    private function getCodigoTipo($tipos, $estrutural)
    {
        $estrutural = new Estrutural($estrutural);
        $nivel = $estrutural->getNivel();
    
        while ($nivel > 2) {
            foreach ($tipos as $tipo) {
                if ($tipo->e90_estrut == $estrutural->getEstruturalAteNivel()) {
                    return $tipo->e90_codigo;
                }
            }
            $estrutural = $estrutural->getEstruturalPai();
            $nivel = $estrutural->getNivel();
        }
    
        return 999;
    }

    private function processaRestoAnterior($restoAnterior)
    {
        $sql = <<<SQL
        select
            c71_coddoc as documento,
            sum(c70_valor) as total
        from
            empresto
            inner join empempenho on e60_numemp = e91_numemp
            inner join conlancamemp on c75_numemp = e91_numemp
            inner join conlancamdoc on c75_codlan = c71_codlan
            inner join conlancam on c70_codlan = c71_codlan
        where
            e91_anousu = {$this->exercicioAtual}
            and e91_numemp = {$restoAnterior->e91_numemp}
            and c71_data between '{$this->exercicioAtual}-01-01' and '{$this->exercicioAtual}-12-31'
        group by
            e91_numemp,
            c71_coddoc
SQL;

        $dados = DB::select($sql);
        $anulado = 0;
        $liquidado = 0;
        $pago = 0;

        foreach ($dados as $dado) {
            if (in_array($dado->documento, [31, 32])) {
                $anulado += $dado->total;
            }

            if (in_array($dado->documento, [33, 39])) {
                $liquidado += $dado->total;
            }

            if (in_array($dado->documento, [31, 34, 40])) {
                $liquidado -= $dado->total;
            }

            if (in_array($dado->documento, [6008, 6010, 35, 37])) {
                $pago += $dado->total;
            }

            if (in_array($dado->documento, [6009, 6011, 38, 36])) {
                $pago -= $dado->total;
            }
        }

        if (round(round(round($restoAnterior->e91_vlremp, 2) - round($restoAnterior->e91_vlranu + $anulado, 2), 2)
            - round(($restoAnterior->e91_vlrpag + $pago), 2), 2) > 0
        ) {
            $restoAnterior->e91_rpcorreto = ($restoAnterior->e91_rpcorreto == "t") ? "true" : "false";

            $restoAnterior->e91_vlranu += $anulado;
            $restoAnterior->e91_vlrliq += $liquidado;
            $restoAnterior->e91_vlrpag += $pago;
            $resto = new EmpenhoResto();
            $resto->e91_anousu = $this->proximoExercicio;
            $resto->e91_numemp = $restoAnterior->e91_numemp;
            $resto->e91_vlremp = $restoAnterior->e91_vlremp;
            $resto->e91_vlranu = "$restoAnterior->e91_vlranu";
            $resto->e91_vlrliq = "$restoAnterior->e91_vlrliq";
            $resto->e91_vlrpag = "$restoAnterior->e91_vlrpag";
            $resto->e91_elemento = "$restoAnterior->e91_elemento";
            $resto->e91_recurso = $restoAnterior->e91_recurso;
            $resto->e91_codtipo = $restoAnterior->e91_codtipo;
            $resto->e91_rpcorreto = $restoAnterior->e91_rpcorreto;
            $resto->save();
        }
    }

    private function processsaOrigemRestos()
    {
        /**
         * Percorre os empenhos inscritos em RP e cria a origem do recurso do RP
         */
        $sql = <<<SQL
            select
                e91_numemp as empenho,
                e91_recurso as recurso,
                o15_complemento as complemento
            from
                empresto
                join orctiporec on o15_codigo = e91_recurso
            where
                e91_anousu = {$this->proximoExercicio}
SQL;
        $dados = DB::select($sql);

        foreach ($dados as $dado) {
            $origem = OrigemComplementoRecurso::where('o206_numero', $dado->empenho)
                ->where('o206_recurso', $dado->recurso)
                ->where('o206_complementorecurso', $dado->complemento)
                ->where('o206_origem', OrigemComplementoRecurso::EMPENHO_RP)
                ->first();
            if (empty($origem)) {
                $origem = new OrigemComplementoRecurso();
                $origem->o206_numero = $dado->empenho;
                $origem->o206_recurso = $dado->recurso;
                $origem->o206_complementorecurso = $dado->complemento;
                $origem->o206_origem = OrigemComplementoRecurso::EMPENHO_RP;
                $origem->save();
            }
        }
    }
}
