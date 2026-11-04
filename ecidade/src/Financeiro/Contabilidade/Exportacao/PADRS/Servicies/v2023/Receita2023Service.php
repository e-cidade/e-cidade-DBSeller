<?php

namespace ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\v2023;

use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Builders\v2023\Receita2023Builder;
use ECidade\Financeiro\Contabilidade\Exportacao\PADRS\Servicies\PadService;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaFormatter;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use Exception;
use Illuminate\Support\Collection;
use Instituicao;

class Receita2023Service extends PadService
{
    protected $fileName = 'RECEITA.TXT';
    /**
     * Fontes de receita
     * @var array
     */
    protected $fontesReceitas;

    public function __construct($exercicio, $instituicoes, $dataInicio, $dataFim)
    {
        $this->instituicoes = $instituicoes;
        $this->ano = $exercicio;
        $this->dataInicial = $dataInicio;
        $this->dataFinal = $dataFim;
    }

    /**
     * Monta o sql para buscar as informações
     * @return string
     * @throws Exception
     */
    protected function getSql()
    {

        $where = [
            "o70_anousu = {$this->ano}",
            'uniao is false',
            sprintf("o70_instit in (%s)", implode(', ', array_map(function (Instituicao $instituicao) {
                return $instituicao->getCodigo();
            }, $this->instituicoes))),
            sprintf(
                "(%s or %s or %s or %s)",
                'previsao_adicional_acumulado != 0',
                'valor_a_arrecadar != 0',
                'arrecadado_periodo != 0',
                'arrecadado_acumulado != 0'
            )
        ];
        $where = implode(' and ', $where);

        $camposArrecadacao = $this->getCamposArrecadacao();

        /**
         * Esse sql esta ignorando o recurso e complemento da arrecadação (lançamento) agrupando os valores pelo recurso
         * da receita
         */
        //$campoSubrecurso = $this->emissaoMGS ? 'o15_recurso as subrecurso' : "'0000' as subrecurso";

        $campoSubrecurso = "'0000' as subrecurso";
        $joinOrctiporec = "";
        if ($this->emissaoMGS) {
            $campoSubrecurso = "o15_recurso as subrecurso";
            $joinOrctiporec = "join orctiporec on o15_codigo = o70_codigo";
        }

        $sql = "
        with valores as (
            SELECT o70_anousu AS exercicio,
                   conta AS natureza,
                   nome AS descricao,
                   $campoSubrecurso,
                   substr(siconfi, 2)::varchar as siconfi,
                   case when o200_tribunal is true then complemento_lancamento
                       else 0
                   end as complemento,
                   o70_concarpeculiar AS cp,
                   o70_instit AS instituicao,
                   codtrib as orgao_unidade,
                   {$camposArrecadacao},
                   coalesce(janeiro, 0) + coalesce(fevereiro, 0) as bimestre_1,
                   coalesce(marco, 0) + coalesce(abril, 0) as bimestre_2,
                   coalesce(maio, 0) + coalesce(junho, 0) as bimestre_3,
                   coalesce(julho, 0) + coalesce(agosto, 0) as bimestre_4,
                   coalesce(setembro, 0) + coalesce(outubro, 0) as bimestre_5,
                   coalesce(novembro, 0) + coalesce(dezembro, 0) as bimestre_6
            from orcreceita
            join db_config on db_config.codigo = o70_instit
            left join acompanhamentocronogramareceita on (exercicio, receita_id) = (o70_anousu, o70_codrec)
            join conplanoorcamento on (c60_codcon, c60_anousu) = (o70_codfon, o70_anousu)
            join planoreceitaconplanoorcamento on conplanoorcamento_codigo = c60_codigo
            join planoreceita on planoreceita.id = planoreceita_id
            join balancete_receita_complemento(
                    o70_anousu, o70_codfon, o70_concarpeculiar, '{$this->dataInicial}', '$this->dataFinal'
                 ) as bl on o70_codfon = bl.fonte
            join complementofonterecurso ON complementofonterecurso.o200_sequencial = bl.complemento_lancamento
            $joinOrctiporec
            where $where
        ), soma as (
        select
                exercicio,
                natureza,
                descricao,
                subrecurso,
                siconfi,
                complemento,
                cp,
                instituicao,
                orgao_unidade,
                sum(arrecadado_jan) as arrecadado_jan,
                sum(arrecadado_fev) as arrecadado_fev,
                sum(arrecadado_mar) as arrecadado_mar,
                sum(arrecadado_abr) as arrecadado_abr,
                sum(arrecadado_mai) as arrecadado_mai,
                sum(arrecadado_jun) as arrecadado_jun,
                sum(arrecadado_jul) as arrecadado_jul,
                sum(arrecadado_ago) as arrecadado_ago,
                sum(arrecadado_set) as arrecadado_set,
                sum(arrecadado_out) as arrecadado_out,
                sum(arrecadado_nov) as arrecadado_nov,
                sum(arrecadado_dez) as arrecadado_dez,
                sum(bimestre_1) as bimestre_1,
                sum(bimestre_2) as bimestre_2,
                sum(bimestre_3) as bimestre_3,
                sum(bimestre_4) as bimestre_4,
                sum(bimestre_5) as bimestre_5,
                sum(bimestre_6) as bimestre_6
            from valores
            group by 1, 2, 3, 4, 5, 6, 7, 8, 9
            order by 3
        ) select *
            from soma
        ";

        //echo $sql; die();
        return $sql;
    }

    /**
     * Monta a string para pesquisa dos valores arrecadados no período por Mês
     * @return string
     * @throws Exception
     */
    protected function getCamposArrecadacao()
    {
        $camposArrecadado = [];

        $dataFim = new \DBDate($this->dataFinal);
        foreach (\DBDate::getMesesAbreviado() as $mes => $nome) {
            $label = sprintf('arrecadado_%s', strtolower($nome));
            // não deve buscar o valor arrecadado dos meses posteriores ao mês selecionado do pad
            if ($mes > (int)$dataFim->getMes()) {
                $campo = "(0) as {$label}";
                $camposArrecadado[] = $campo;
                continue;
            }

            // busca do balancete da receita
            $mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
            $fim = sprintf(
                '%s-%s-%s',
                $this->ano,
                $mes,
                cal_days_in_month(CAL_GREGORIAN, $mes, $this->ano)
            );
            $inicio = "{$this->ano}-{$mes}-01";
            $campo = "(select coalesce(sum(arrecadado_periodo), 0)
            from balancete_receita_complemento(o70_anousu, o70_codfon, o70_concarpeculiar, '{$inicio}', '{$fim}') as bp
            where bp.recurso_lancamento = bl.recurso_lancamento
            ) as $label
            ";
            $camposArrecadado[] = $campo;
        }

        return implode(',', $camposArrecadado);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function processaDados()
    {
        $rs = db_query($this->getSql());
        if (!$rs && pg_num_rows($rs) === 0) {
            throw new Exception('Erro ao buscar as receitas.');
        }
        return $this->montaArvore(\db_utils::getCollectionByRecord($rs));
    }

    protected function estruturalFormatter($natureza)
    {
        return new EstruturalReceitaPadrao($natureza);
    }

    protected function montaArvore($receitas)
    {
        $arvore = [];
        foreach ($receitas as $receita) {
            $estrutural = $this->estruturalFormatter($receita->natureza);
            $nivel = $estrutural->getNivel();

            $hash = sprintf(
                '%s#%s#%s#%s',
                $receita->natureza,
                $receita->cp,
                $receita->siconfi,
                $receita->complemento
            );
            $arvore[$hash] = $this->mapperReceitaAnalitica($receita);
            list($estrutural, $arvore) = $this->montaContaPai($nivel, $estrutural, $arvore, $receita);
        }
        ksort($arvore);
        return $arvore;
    }

    protected function mapperReceitaAnalitica($receita)
    {
        $std = $this->criaObjetoReceita();

        $std->exercicio = $receita->exercicio;
        $std->natureza = $receita->natureza;
        $std->descricao = $receita->descricao;
        $std->subrecurso = $receita->subrecurso;
        $std->siconfi = $receita->siconfi;
        $std->complemento = $receita->complemento;
        $std->cp = $receita->cp;
        $std->instituicao = $receita->instituicao;
        $std->orgao_unidade = $receita->orgao_unidade;
        $std->arrecadado_jan = $receita->arrecadado_jan;
        $std->arrecadado_fev = $receita->arrecadado_fev;
        $std->arrecadado_mar = $receita->arrecadado_mar;
        $std->arrecadado_abr = $receita->arrecadado_abr;
        $std->arrecadado_mai = $receita->arrecadado_mai;
        $std->arrecadado_jun = $receita->arrecadado_jun;
        $std->arrecadado_jul = $receita->arrecadado_jul;
        $std->arrecadado_ago = $receita->arrecadado_ago;
        $std->arrecadado_set = $receita->arrecadado_set;
        $std->arrecadado_out = $receita->arrecadado_out;
        $std->arrecadado_nov = $receita->arrecadado_nov;
        $std->arrecadado_dez = $receita->arrecadado_dez;
        $std->bimestre_1 = $receita->bimestre_1;
        $std->bimestre_2 = $receita->bimestre_2;
        $std->bimestre_3 = $receita->bimestre_3;
        $std->bimestre_4 = $receita->bimestre_4;
        $std->bimestre_5 = $receita->bimestre_5;
        $std->bimestre_6 = $receita->bimestre_6;
        $std->sintetico = false;

        return $std;
    }

    protected function montaContaPai($nivel, $estrutural, array $arvore, $receita)
    {
        while ($nivel != 1) {
            $estrutural = $this->estruturalFormatter($estrutural->getCodigoEstruturalPai());
            $fonteReceita = $this->buscaFonteReceita($estrutural->getEstrutural());
            $estrutural = $this->estruturalFormatter($fonteReceita->natureza);

            $hash = $fonte = $estrutural->getEstrutural();
            $nivel = $estrutural->getNivel();

            if (!array_key_exists($fonte, $arvore)) {
                $arvore[$hash] = $this->mapperReceitaSintetica($fonteReceita->nome, $estrutural);
            }

            $arvore[$hash]->arrecadado_jan += $receita->arrecadado_jan;
            $arvore[$hash]->arrecadado_fev += $receita->arrecadado_fev;
            $arvore[$hash]->arrecadado_mar += $receita->arrecadado_mar;
            $arvore[$hash]->arrecadado_abr += $receita->arrecadado_abr;
            $arvore[$hash]->arrecadado_mai += $receita->arrecadado_mai;
            $arvore[$hash]->arrecadado_jun += $receita->arrecadado_jun;
            $arvore[$hash]->arrecadado_jul += $receita->arrecadado_jul;
            $arvore[$hash]->arrecadado_ago += $receita->arrecadado_ago;
            $arvore[$hash]->arrecadado_set += $receita->arrecadado_set;
            $arvore[$hash]->arrecadado_out += $receita->arrecadado_out;
            $arvore[$hash]->arrecadado_nov += $receita->arrecadado_nov;
            $arvore[$hash]->arrecadado_dez += $receita->arrecadado_dez;
            $arvore[$hash]->bimestre_1 += $receita->bimestre_1;
            $arvore[$hash]->bimestre_2 += $receita->bimestre_2;
            $arvore[$hash]->bimestre_3 += $receita->bimestre_3;
            $arvore[$hash]->bimestre_4 += $receita->bimestre_4;
            $arvore[$hash]->bimestre_5 += $receita->bimestre_5;
            $arvore[$hash]->bimestre_6 += $receita->bimestre_6;
        }
        return array($estrutural, $arvore);
    }

    private function mapperReceitaSintetica($nomeFonteReceita, EstruturalReceitaFormatter $estrutural)
    {
        $std = $this->criaObjetoReceita();
        $std->natureza = $estrutural->getEstrutural();
        $std->descricao = $nomeFonteReceita;
        return $std;
    }

    private function criaObjetoReceita()
    {
        return (object)[
            'exercicio' => null,
            'natureza' => null,
            'descricao' => '',
            'subrecurso' => null,
            'siconfi' => null,
            'complemento' => null,
            'cp' => '000',
            'instituicao' => null,
            'orgao_unidade' => null,
            'arrecadado_jan' => 0,
            'arrecadado_fev' => 0,
            'arrecadado_mar' => 0,
            'arrecadado_abr' => 0,
            'arrecadado_mai' => 0,
            'arrecadado_jun' => 0,
            'arrecadado_jul' => 0,
            'arrecadado_ago' => 0,
            'arrecadado_set' => 0,
            'arrecadado_out' => 0,
            'arrecadado_nov' => 0,
            'arrecadado_dez' => 0,
            'bimestre_1' => 0,
            'bimestre_2' => 0,
            'bimestre_3' => 0,
            'bimestre_4' => 0,
            'bimestre_5' => 0,
            'bimestre_6' => 0,
            'sintetico' => true,
        ];
    }

    /**
     * @param $fonte
     * @return mixed
     */
    protected function buscaFonteReceita($fonte)
    {
        if (!array_key_exists($fonte, $this->getFonteReceitas())) {
            $estruturalPai = $this->estruturalFormatter($fonte)->getEstruturalPai();
            $fonte = $estruturalPai->getEstrutural();
            return $this->buscaFonteReceita($fonte);
        }
        return $this->fontesReceitas[$fonte];
    }

    /**
     * @return array
     */
    protected function getFonteReceitas()
    {
        if (is_null($this->fontesReceitas)) {
            PlanoReceita::where('uniao', false)
                ->where('exercicio', $this->ano)
                ->orderBy('conta')
                ->get()
                ->each(function ($ementario) {
                    $estrutural = $this->estruturalFormatter($ementario->natureza)->getEstrutural();
                    $this->fontesReceitas[$estrutural] = $ementario;
                });
        }
        return $this->fontesReceitas;
    }

    protected function getDados()
    {
        $receitas = $this->processaDados();

        foreach ($receitas as $receita) {
            $builder = $this->getBuilder();
            yield $builder->addDados((array)$receita)
                ->addModelo($this->emissaoMGS)
                ->build();
        }
    }

    protected function getBuilder()
    {
        return new Receita2023Builder();
    }
}
