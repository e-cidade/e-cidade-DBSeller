<?php

namespace ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2020;

use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres\AnexoTresService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTresInRsService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres\AnexoTresMdfService;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2018\AnexoXIII as AnexoXIII2018;
use Exception;
use InstituicaoRepository;
use Periodo;

/**
 * Class AnexoXIII
 *
 * @package ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2020
 */
class AnexoXIII extends AnexoXIII2018
{

    /**
     * @var integer
     */
    const CODIGO_RELATORIO = 218;

    /**
     * @return array
     * @throws \Exception
     */
    public function getLinhas()
    {
        $this->getDados();
        $this->processar();
        return $this->aLinhasConsistencia;
    }

    /**
     * @throws \Exception
     */
    protected function processar()
    {

        $linhasDePara = array(12 => 12, 13 => 13);

        $linhas = array_slice($this->aLinhasConsistencia, 0, 13, true);
        $this->adicionaLinhasDespesasPPP(13, $linhas);

        $linhas[] = $this->aLinhasConsistencia[14];
        $linhasDePara[14] = count($linhas);
        $this->adicionaLinhasDespesasPPP(14, $linhas);

        $linhas[] = $this->aLinhasConsistencia[15];
        $linhasDePara[15] = count($linhas);

        $linhas[] = $this->aLinhasConsistencia[16];
        $linhasDePara[16] = count($linhas);
        $this->adicionaLinhasDespesasPPP(16, $linhas);

        $linhas[] = $this->aLinhasConsistencia[17];
        $linhasDePara[17] = count($linhas);
        $this->adicionaLinhasDespesasPPP(17, $linhas);

        $linhas[] = $this->aLinhasConsistencia[18];
        $linhasDePara[18] = count($linhas);
        $ini = 19;
        $fim = 21;
        for ($linha = $ini; $linha <= $fim; $linha++) {
            $linhas[] = $this->aLinhasConsistencia[$linha];
            $linhasDePara[$linha] = count($linhas);
        }
        $this->aLinhasConsistencia = $this->atualizarOrdens($linhas);
        $this->calcularRCL($linhasDePara[19]);
        $this->calcularTotalizadorLinhasDinamicas($linhasDePara);
    }

    protected function calcularRCL($linhaRCL)
    {
        $anoAnterior = ($this->iAnoUsu - 1);
        $instituicoes = \InstituicaoRepository::getInstituicoes();
        $codigoInstituicoes = implode(',', array_keys($instituicoes));
    
        $calc = $this->linhasAdicionadasEnteFederado + $this->linhasAdicionadasEstatais - 2;
        $linhaReceitaCorrenteLiquidaAtualizada = static::LINHA_RECEITA_CORRENTE_LIQUIDA_RCL_III + $calc;
        $linhaReceitaCorrenteLiquida = $this->aLinhasConsistencia[$linhaReceitaCorrenteLiquidaAtualizada];
        if ($this->getAno() >= 2020) {
            $linhaReceitaCorrenteLiquida = $this->aLinhasConsistencia[$linhaRCL];
        }

        if (empty($linhaReceitaCorrenteLiquida->exercicio_anterior)) {
            $serviceRCLAnterior = $this->getServiceNovoRCL(Periodo::SEGUNDO_SEMESTRE, $this->getAno() -1);
            $simplificadoAnterior = $serviceRCLAnterior->processaLinhasSimplificado();
            $linhaReceitaCorrenteLiquida->exercicio_anterior = $simplificadoAnterior[0]->ate_bimestre;
        }

        if (empty($linhaReceitaCorrenteLiquida->exercicio_corrente)) {
            $serviceRCLExercicio = $this->getServiceNovoRCL($this->getPeriodo()->getCodigo(), $this->getAno());
            $simplificadoExercicio = $serviceRCLExercicio->processaLinhasSimplificado();
            $linhaReceitaCorrenteLiquida->exercicio_corrente = $simplificadoExercicio[0]->ate_bimestre;
        }
    }

    protected function calcularTotalizadorLinhasDinamicas($dePara)
    {
        $colunas = array(
        'exercicio_anterior',
        'exercicio_corrente'
        );
        for ($i = 1; $i <= 9; $i++) {
            $colunas[] = "exercicio_corrente_{$i}";
        }

        foreach ($colunas as $nomeColuna) {
            $linhaFilha1 = $this->aLinhasConsistencia[$dePara[13]]->$nomeColuna;
            $linhaFilha2 = $this->aLinhasConsistencia[$dePara[14]]->$nomeColuna;
            $this->aLinhasConsistencia[$dePara[12]]->$nomeColuna = round($linhaFilha1 + $linhaFilha2, 2);

            $linhaFilha1 = $this->aLinhasConsistencia[$dePara[16]]->$nomeColuna;
            $linhaFilha2 = $this->aLinhasConsistencia[$dePara[17]]->$nomeColuna;
            $this->aLinhasConsistencia[$dePara[15]]->$nomeColuna = round($linhaFilha1 + $linhaFilha2, 2);

            $valor = $this->aLinhasConsistencia[$dePara[12]]->$nomeColuna +
            $this->aLinhasConsistencia[$dePara[15]]->$nomeColuna;
            $this->aLinhasConsistencia[$dePara[18]]->$nomeColuna = round($valor, 2);

            $this->aLinhasConsistencia[$dePara[20]]->$nomeColuna = $this->aLinhasConsistencia[$dePara[12]]->$nomeColuna;
        }

        $linha20 = $this->aLinhasConsistencia[$dePara[20]]->exercicio_anterior;
        $linha19 = $this->aLinhasConsistencia[$dePara[19]]->exercicio_anterior;
        $this->aLinhasConsistencia[$dePara[21]]->exercicio_anterior = 0;
        if ($linha19 > 0) {
            $this->aLinhasConsistencia[$dePara[21]]->exercicio_anterior = $linha20 / $linha19;
        }

        $linha20 = $this->aLinhasConsistencia[$dePara[20]]->exercicio_corrente;
        $linha19 = $this->aLinhasConsistencia[$dePara[19]]->exercicio_corrente;
        $this->aLinhasConsistencia[$dePara[21]]->exercicio_corrente = 0;
        if ($linha19 > 0) {
            $this->aLinhasConsistencia[$dePara[21]]->exercicio_corrente = $linha20 / $linha19;
        }
    }


    /**
     * @param $linha
     * @param $linhas
     */
    protected function adicionaLinhasDespesasPPP($linha, &$linhas)
    {
        $codigoRelatorio = self::CODIGO_RELATORIO;
        $codigoPeriodo = $this->getPeriodo()->getCodigo();
        $codigoInstituicao = $this->getInstituicoes();

        $sql = "
            SELECT
              o117_linha,
              o116_ordem,
              o117_instit,
              o117_valor
            FROM orcparamseqorcparamseqcoluna
              INNER JOIN configuracoes.orcparamseqorcparamseqcolunavalor
              ON o117_orcparamseqorcparamseqcoluna = o116_sequencial
            WHERE o116_codparamrel = {$codigoRelatorio}
            AND o116_codseq = {$linha}
            AND o116_periodo = {$codigoPeriodo}
            AND o117_instit IN ({$codigoInstituicao})
            ORDER BY o117_linha, o116_ordem
        ";

        $rsDespesasPPEnteFederado = db_query($sql);

        if (!$rsDespesasPPEnteFederado) {
            throw new Exception('Não foi possível buscar as linhas dos entes federados.');
        }

        $aLinhasRetornadas = array();

        while ($dados = pg_fetch_object($rsDespesasPPEnteFederado)) {
            $aLinhasRetornadas[$dados->o117_instit][$dados->o117_linha][$dados->o116_ordem] = $dados->o117_valor;
        }

        $linhasAdicionadas = 0;
        foreach ($aLinhasRetornadas as $aLinhasInstituicao) {
            foreach ($aLinhasInstituicao as $linhaRetornada) {
                $novaLinhaEnteFederado = clone $this->aLinhasConsistencia[$linha];
                $novaLinhaEnteFederado->oLinhaRelatorio = clone $this->aLinhasConsistencia[$linha]->oLinhaRelatorio;
                $novaLinhaEnteFederado->descricao = $linhaRetornada[1];
                $novaLinhaEnteFederado->oLinhaRelatorio->setDescricaoLinha($linhaRetornada[1]);
                $novaLinhaEnteFederado->exercicio_anterior = $linhaRetornada[2];
                $novaLinhaEnteFederado->exercicio_corrente = $linhaRetornada[3];
                $novaLinhaEnteFederado->exercicio_corrente_1 = $linhaRetornada[4];
                $novaLinhaEnteFederado->exercicio_corrente_2 = $linhaRetornada[5];
                $novaLinhaEnteFederado->exercicio_corrente_3 = $linhaRetornada[6];
                $novaLinhaEnteFederado->exercicio_corrente_4 = $linhaRetornada[7];
                $novaLinhaEnteFederado->exercicio_corrente_5 = $linhaRetornada[8];
                $novaLinhaEnteFederado->exercicio_corrente_6 = $linhaRetornada[9];
                $novaLinhaEnteFederado->exercicio_corrente_7 = $linhaRetornada[10];
                $novaLinhaEnteFederado->exercicio_corrente_8 = $linhaRetornada[11];
                $novaLinhaEnteFederado->exercicio_corrente_9 = $linhaRetornada[12];
                unset($novaLinhaEnteFederado->despesas_ppp);
                $linhas[] = $novaLinhaEnteFederado;
                $linhasAdicionadas++;
            }
        }
        return $linhasAdicionadas;
    }

    /**
     * @param $codigoPeriodo
     * @param $exercicio
     * @return AnexoTresService
     * @throws Exception
     */
    private function getServiceNovoRCL($codigoPeriodo, $exercicio)
    {
        $filtros = [
        'codigo_relatorio' => AnexoTresFactory::getCodigoRelatorio($exercicio),
        'periodo' => AnexoTresFactory::transformPeriodo($codigoPeriodo),
        'DB_anousu' => $exercicio,
        'DB_instit' => db_getsession('DB_instit')
        ];
        return AnexoTresFactory::getService($exercicio, $filtros);
    }
}
