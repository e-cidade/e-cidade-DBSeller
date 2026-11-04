<?php

namespace App\Domain\Financeiro\Orcamento\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Models\PlanoReceita;
use App\Domain\Financeiro\Orcamento\Models\FonteReceita;
use App\Domain\Financeiro\Orcamento\Models\Receita;
use App\Domain\Financeiro\Orcamento\Relatorios\Anexos\AnexoTresCsv;
use App\Domain\Financeiro\Orcamento\Relatorios\Anexos\AnexoTresPdf;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceita;
use ECidade\Financeiro\Contabilidade\PlanoDeContas\EstruturalReceitaPadrao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

/**
 * Relatório cadastral da receita
 */
class AnexoTresService extends AnexosService
{

    public function emitir()
    {
        $ementario = $this->processar();
        return array_merge($this->emitirPdf($ementario), $this->emitirCsv($ementario));
    }

    protected function processar()
    {
        $dados = $this->getDados();
        return $this->montaArvore($dados);
    }

    protected function montaArvore($receitas)
    {
        $arvore = [];
        foreach ($receitas as $receita) {
            $estrutural = $this->estruturalFormatter($receita->natureza);
            $hash = $estrutural->getEstrutural();
            $arvore[$estrutural->getEstrutural()] = (object)(object)[
                'natureza' => $estrutural->getEstrutural(),
                'nome' => $receita->nome,
            ];

            $nivel = $estrutural->getNivel();

            list($estrutural, $arvore) = $this->montaContaPai($nivel, $estrutural, $arvore, $receita);
        }

        ksort($arvore);
        return $arvore;
    }

    protected function montaContaPai($nivel, $estrutural, array $arvore, $receita)
    {
        while ($nivel != 1) {
            $estrutural = $this->estruturalFormatter($estrutural->getCodigoEstruturalPai());
            $fonteReceita = $this->buscaFonteReceita($estrutural->getEstrutural());
            $estrutural = $this->estruturalFormatter($fonteReceita->natureza);
            $fonte = $estrutural->getEstrutural();
            $nivel = $estrutural->getNivel();

            if (!array_key_exists($fonte, $arvore)) {
                $arvore[$fonte] = (object)[
                    'natureza' => $this->estruturalFormatter($fonteReceita->natureza)->getEstrutural(),
                    'nome' => $fonteReceita->nome,
                ];
            }
        }
        return array($estrutural, $arvore);
    }

    protected function buscaFonteReceita($fonte)
    {
        if (!array_key_exists($fonte, $this->getFontesReceitas())) {
            $estruturalPai = $this->estruturalFormatter($fonte)->getEstruturalPai();
            $fonte = $estruturalPai->getEstrutural();
            return $this->buscaFonteReceita($fonte);
        }
        return $this->fontesReceitas[$fonte];
    }

    protected function getDados()
    {
        if ($this->ementario === 'ecidade') {
            return $this->getReceitasPlanoEcidade();
        }
        return $this->getReceitasEmentarioPadrao();
    }

    protected function getReceitasPlanoEcidade()
    {
        return Receita::query()
            ->select(['o57_fonte as natureza', 'o57_descr as nome'])
            ->join('orcamento.orcfontes', function (JoinClause $join) {
                $join->on('o57_codfon', 'o70_codfon')
                    ->on('o57_anousu', 'o70_anousu');
            })
            ->whereIn('o70_instit', $this->codigosInstituicoes)
            ->where('o70_anousu', $this->exercicio)
            ->orderBy('o57_fonte')
            ->get();
    }

    protected function getReceitasEmentarioPadrao()
    {
        return PlanoReceita::query()
            ->select(['conta', 'nome'])
            ->distinct()
            ->join('planoreceitaconplanoorcamento', 'planoreceita_id', 'planoreceita.id')
            ->join('conplanoorcamento', 'c60_codigo', 'conplanoorcamento_codigo')
            ->join('orcamento.orcfontes', function (JoinClause $join) {
                $join->on('o57_codfon', 'c60_codcon')
                    ->on('o57_anousu', 'c60_anousu');
            })
            ->join('orcamento.orcreceita', function (JoinClause $join) {
                $join->on('o70_codfon', 'o57_codfon')
                    ->on('o70_anousu', 'o57_anousu');
            })
            ->where('exercicio', $this->exercicio)
            ->where('uniao', $this->ementario === 'uniao')
            ->orderBy('conta')
            ->get();
    }

    protected function estruturalFormatter($natureza)
    {
        if ($this->ementario === 'ecidade') {
            return new EstruturalReceita($natureza);
        }
        return new EstruturalReceitaPadrao($natureza);
    }

    /**
     * @return array
     */
    protected function getFontesReceitas()
    {
        if (is_null($this->fontesReceitas)) {
            $this->fontesReceitas = getFontesEmentario($this->ementario, $this->exercicio);
        }

        return $this->fontesReceitas;
    }

    /**
     * @return array
     */
    public function processaTitulosRelatorios()
    {
        $origemEmentario = $this->getOrigemEmentario();

        $titulos = [
            'RELATÓRIO DAS FONTES DA RECEITA - ANEXO 3',
            'SEGUNDO A CATEGORIA ECONÔMICA',
            "Exercício: $this->exercicio",
            $origemEmentario,
            sprintf("Instituições: %s", implode(', ', $this->nomesInstituicoes)),
        ];
        return $titulos;
    }

    /**
     * Emite o pdf do ementário
     * @param array $ementario
     * @return array
     */
    protected function emitirPdf($ementario)
    {
        $relatorio = new AnexoTresPdf();
        return $relatorio->addTitulos($this->titulosRelatorio)
            ->addEmentario($ementario)
            ->emitir();
    }

    /**
     * Emite o csv do ementário
     * @param $ementario
     * @return array
     */
    protected function emitirCsv($ementario)
    {
        $relatorio = new AnexoTresCsv();
        return $relatorio->setEmentario($ementario)
            ->setTitulos($this->titulosRelatorio)
            ->emitir();
    }
}
