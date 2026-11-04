<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2024;

use App\Domain\Financeiro\Contabilidade\Models\Conplano;
use ECidade\Enum\Financeiro\Contabilidade\PcaspIndicadorSuperavitEnum;
use ECidade\Enum\Financeiro\Contabilidade\PcaspNaturezaInformacaoEnum;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2024\BalanceteVerificacaoAnterior2024Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\PadService;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalPcaspPadrao;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class BalanceteVerificacaoAnterior2024Service extends PadService
{
    protected $fileName = 'BVER_ANT.TXT';

    /**
     * Todas contas do plano de contas
     * @var Conplano[]|Collection
     */
    protected $planoContas;

    protected $encerramento = false;

    public function __construct($anousu, $instituicoes, $dataInicio, $dataFim)
    {
        $this->ano = $anousu;
        $this->instituicoes = $instituicoes;
        $this->dataInicial = $dataInicio;
        $this->dataFinal = $dataFim;
    }

    public function comEncerrametno()
    {
        $this->encerramento = true;
    }

    protected function getHash(\stdClass $conta)
    {
        return sprintf('%s#%s', $conta->estrutural, $conta->orgao_unidade);
    }

    protected function getDados()
    {
        $dados = $this->processaDadosRelatorio();
        foreach ($dados as $dado) {
            $builder = $this->getBuilder();
            yield $builder->addDados((array)$dado)
                ->build();
        }
    }

    protected function getBuilder()
    {
        return new BalanceteVerificacaoAnterior2024Builder();
    }

    protected function processaDadosRelatorio()
    {
        $dadosBalancete = $this->buscaValoresBalancete();
        return $this->montaArvore($dadosBalancete);
    }
    protected function buscaValoresBalancete()
    {
        $where = sprintf('instituicao in (%s)', $this->getListaInstituicoes());

        $encerramento = $this->encerramento ? 'true' : 'false';

        $sql = "
         with contas as (
            select estrutural,
                   reduzido,
                   exercicio,
                   codigo_conplano,
                   classe,
                   nome,
                   instituicao,
                   substring(siconfi, 2) as siconfi,
                   0 as subrecurso,
                   indicador_superavit,
                   natureza_informacao,
                   saldo_anterior,
                   saldo_debito,
                   saldo_credito,
                   saldo_final,
                   case when o200_tribunal is true then complemento else 0 end as complemento,
                   codtrib as orgao_unidade
              from contabilidade.balancete_verificacao_por_recurso(
                     $this->ano, '$this->dataInicial', '$this->dataFinal', $encerramento, null
                   )
              join configuracoes.db_config on db_config.codigo = instituicao
              join orcamento.complementofonterecurso on o200_sequencial = complemento
            where $where
        ), agrupa as (
            select  estrutural,
                    reduzido,
                    exercicio,
                    nome,
                    indicador_superavit,
                    natureza_informacao,
                    orgao_unidade,
                    sum(saldo_anterior) as saldo_anterior,
                    sum(saldo_debito) as saldo_debito,
                    sum(saldo_credito) as saldo_credito
            from contas
            group by estrutural, reduzido, exercicio, nome, indicador_superavit, natureza_informacao, orgao_unidade
        ) select *
           from (
              select agrupa.*,
                     (saldo_anterior + saldo_debito + saldo_credito) as saldo_final
                from agrupa
            ) as x
        ";

        $rs = db_query($sql);
        if (!$rs) {
            throw new \Exception('Erro ao buscar lançamentos do balancete de verificação.', 400);
        }

        if (pg_num_rows($rs) === 0) {
            throw new \Exception('Sem registros no balancete de verificação.', 400);
        }

        return \db_utils::getCollectionByRecord($rs);
    }


    /**
     * @param array $dadosBalancete
     * @return array
     * @throws Exception
     */
    protected function montaArvore(array $dadosBalancete)
    {
        $arvore = [];

        foreach ($dadosBalancete as $conta) {
            $estrutural = $this->estruturalFormatter($conta->estrutural);
            $nivel = $estrutural->getNivel();
            $hash = $this->getHash($conta);
            $arvore[$hash] = $this->stdContaAnalitica($conta, $nivel);

            list($estrutural, $arvore) = $this->montaContaPai($nivel, $estrutural, $arvore, $conta);
        }

        ksort($arvore);

        foreach ($arvore as $item) {
            if ($item->saldo_anterior >= 0) {
                $item->saldo_anterior_debito = $item->saldo_anterior;
            }
            if ($item->saldo_anterior < 0) {
                $item->saldo_anterior_credito = $item->saldo_anterior * -1;
            }
            if ($item->saldo_credito < 0) {
                $item->saldo_credito = $item->saldo_credito * -1;
            }
            if ($item->saldo_debito < 0) {
                $item->saldo_debito = $item->saldo_debito * -1;
            }
            if ($item->saldo_final >= 0) {
                $item->saldo_final_debito = $item->saldo_final;
            }
            if ($item->saldo_final < 0) {
                $item->saldo_final_credito = $item->saldo_final * -1;
            }
        }

        return $arvore;
    }

    /**
     * @return Conplano[]
     */
    protected function getPlanoContas()
    {
        if (is_null($this->planoContas)) {
            Conplano::apenasSintetica()
                ->where('c60_anousu', $this->ano)
                ->orderBy('c60_estrut')
                ->get()
                ->each(function ($conta) {
                    $estrutural = $this->estruturalFormatter($conta->c60_estrut)->getEstrutural();
                    $this->planoContas[$estrutural] = $conta;
                });
        }

        return $this->planoContas;
    }

    protected function estruturalFormatter($estrutural)
    {
        return new EstruturalPcaspPadrao($estrutural);
    }

    /**
     * @param $nivel
     * @param EstruturalPcaspPadrao $estrutural
     * @param array $arvore arvore montada até o momento
     * @param stdClass $dadosConta Dados da conta que retorna da query
     * @return array
     * @throws Exception
     */
    protected function montaContaPai($nivel, EstruturalPcaspPadrao $estrutural, array $arvore, $dadosConta)
    {
        while ($nivel != 1) {
            $estrutural = $this->estruturalFormatter($estrutural->getCodigoEstruturalPai());
            $conplano = $this->buscaConta($estrutural->getEstrutural());
            $estrutural = $this->estruturalFormatter($conplano->c60_estrut);
            $hash = $estrutural->getEstrutural();
            $nivel = $estrutural->getNivel();

            if (!array_key_exists($hash, $arvore)) {
                $arvore[$hash] = $this->stdContaSintetica($dadosConta, $nivel, $conplano);
            }
            $arvore[$hash]->saldo_anterior += $dadosConta->saldo_anterior;
            $arvore[$hash]->saldo_debito += $dadosConta->saldo_debito;
            $arvore[$hash]->saldo_credito += $dadosConta->saldo_credito;
            $arvore[$hash]->saldo_final += $dadosConta->saldo_final;
        }
        return [$estrutural, $arvore];
    }

    /**
     * @param string $estrutural
     * @return Conplano
     */
    protected function buscaConta($estrutural)
    {
        $planoContas = $this->getPlanoContas();

        if (!array_key_exists($estrutural, $planoContas)) {
            $estruturalPai = $this->estruturalFormatter($estrutural)->getEstruturalPai();
            return $this->buscaConta($estruturalPai->getEstrutural());
        }
        return $planoContas[$estrutural];
    }

    /**
     * @param $dadosConta
     * @param $nivel
     * @return object
     */
    protected function stdConta($dadosConta, $nivel)
    {
        return (object)[
            "estrutural" => $dadosConta->estrutural,
            "exercicio" => $this->ano,
            "nome" => $dadosConta->nome,
            "indicador_superavit" => $dadosConta->indicador_superavit,
            "natureza_informacao" => null,
            "orgao_unidade" => null,
            "sintetica" => false,
            "nivel" => $nivel,
            "saldo_anterior" => 0,
            "saldo_anterior_debito" => 0,
            "saldo_anterior_credito" => 0,
            "saldo_debito" => 0,
            "saldo_credito" => 0,
            "saldo_final" => 0,
            "saldo_final_debito" => 0,
            "saldo_final_credito" => 0,
        ];
    }

    /**
     * @param stdClass $dadosConta
     * @param $nivel
     * @return object
     */
    protected function stdContaAnalitica(stdClass $dadosConta, $nivel)
    {
        $std = $this->stdConta($dadosConta, $nivel);
        $std->orgao_unidade = $dadosConta->orgao_unidade;
        $std->saldo_anterior = $dadosConta->saldo_anterior;
        $std->saldo_debito = $dadosConta->saldo_debito;
        $std->saldo_credito = $dadosConta->saldo_credito;
        $std->saldo_final = $dadosConta->saldo_final;
        $std->indicador_superavit = PcaspIndicadorSuperavitEnum::getInstance($dadosConta->indicador_superavit)->value();
        $std->natureza_informacao = PcaspNaturezaInformacaoEnum::getPorEstrutural($dadosConta->estrutural)->value();
        return $std;
    }

    /**
     * @param stdClass $dadosConta
     * @param $nivel
     * @param Conplano $conplano
     * @return object
     */
    protected function stdContaSintetica(stdClass $dadosConta, $nivel, Conplano $conplano)
    {
        $std = $this->stdConta($dadosConta, $nivel);
        $std->sintetica = true;
        $std->estrutural = $conplano->c60_estrut;
        $std->nome = $conplano->c60_descr;
        $std->indicador_superavit = 'P';
        $std->natureza_informacao = PcaspNaturezaInformacaoEnum::getPorEstrutural($conplano->c60_estrut)->value();

        return $std;
    }
}
