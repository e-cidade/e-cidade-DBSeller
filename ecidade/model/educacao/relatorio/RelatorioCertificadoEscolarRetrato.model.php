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

use \ECidade\Pdf\Pdf;
/**
 * Renderiza o certificado escolar no formato de retrato de acordo com os parâmetros
 *
 * @package educacao
 * @subpackage relatorio
 * @author andrio.costa <andrio.costa@dbseller.com.br>
 * @version $Revision: 1.11 $
 */
class RelatorioCertificadoEscolarRetrato extends RelatorioHistoricoEscolarRetrato {


    public function __construct(Pdf $oPdf, Aluno $oAluno, Escola $oEscola, $iTipoRelatorio, $lExibirReclassificacao)
    {
        parent::__construct($oPdf, $oAluno, $oEscola, $iTipoRelatorio, $lExibirReclassificacao);
        $this->setExibirSomenteCursosEncerrados(true);
        $this->setTitulo("Certificado Escolar");
    }

    /**
     * Monta o quadro das observações
     * Ordem das informações
     * - Observação dos Parâmetros
     * - Convenções (Removido a pedido do Tiago)
     * - Observação do Histórico
     * - Aprovado pelo conselho
     * - Colocar na observação se os dados da trocou de série se houver
     * @throws Exception
     */
    public function montaQuadroObservacao()
    {
        $sObsParametros = $this->oParametros->observacao;
        $sObsHistorico = implode("\n", $this->aObservacaoHistorico);
        $sProAprovacaoComProgressao = "";
        if ($this->lAlunoTeveAprovacaoComProgressao) {
            $sProAprovacaoComProgressao .= " * = Aprovado com progressão parcial / Dependência";
        }
        $sObsAprovadoPeloConselho = "";

        if ($this->oParametros->exibe_obs_diario == 't') {
            $sObsAprovadoPeloConselho = $this->getObservacaoAprovadoPeloConselho();
        }
        $sObsTrocaSerie = $this->getObservacaoTrocaSerie();

        $disciplinasAbreviadas = $this->buscarDisciplinasAbreviadas();

        $sObservacao = [];

        if (!empty($disciplinasAbreviadas)) {
            $sObservacao = ['Legenda: '];
            $sObservacao = array_merge($sObservacao, $disciplinasAbreviadas);
        }
        $sObservacao[] = "Observações: ";
        if (!empty($sObsParametros)) {
            $sObservacao[] = $sObsParametros;
        }
        if (!empty($sProAprovacaoComProgressao)) {
            $sObservacao[] = $sProAprovacaoComProgressao;
        }
        if (!empty($sObsHistorico)) {
            $sObservacao[] = $sObsHistorico;
        }
        if (!empty($sObsAprovadoPeloConselho)) {
            $sObservacao[] = $sObsAprovadoPeloConselho;
        }
        if (!empty($sObsTrocaSerie)) {
            $sObservacao[] = $sObsTrocaSerie;
        }
        foreach ($this->getObservacaoHistoricoEtapa() as $observacaoEtapa) {
            $linhas = $this->oPdf->quebrarTextoEmLinhas(195, $observacaoEtapa);
            $sObservacao = array_merge($sObservacao, $linhas);
        }

        /*PLUGIN DIARIO PROGRESSAO - ADICIONADO OBSERVAÇÕES DA EVASÃO DA PROGRESSÃO - NÃO APAGAR*/

        $this->oPdf->SetFontSize($this->oParametros->fonte_observacao);

        $iAlturaDisponivel = $this->oPdf->getAvailHeight();

        $this->oPdf->SetAutoPageBreak(true, 15);
        if ($iAlturaDisponivel < 15) {
            $this->oPdf->addPage();
            $this->escreveCabecalho();
            $this->oPdf->ln();
        }

        $this->oPdf->line($this->oPdf->getX(), $this->oPdf->getY(), 203, $this->oPdf->getY());
        foreach ($sObservacao as $observacao) {
            $iTotalLinhasObservacao = $this->oPdf->nbLines(195, $observacao);
            $iAlturaLinhasObservacao = $iTotalLinhasObservacao * self::ALTURA_LINHA;
            $iAlturaDisponivel = $this->oPdf->getAvailHeight();
            if ($iAlturaLinhasObservacao >= $iAlturaDisponivel) {
                $this->oPdf->line($this->oPdf->getX(), $this->oPdf->getY(), 203, $this->oPdf->getY());
                $this->oPdf->addPage();
                $this->escreveCabecalho();
                $this->oPdf->ln();
                $this->oPdf->line($this->oPdf->getX(), $this->oPdf->getY(), 203, $this->oPdf->getY());
            }

            $yAntes = $this->oPdf->getY();
            $this->oPdf->writeHTML($observacao . '<br>');
            $yDepois = $this->oPdf->getY();
            $this->oPdf->line(8, $yAntes, 8, $yDepois);
            $this->oPdf->line(203, $yAntes, 203, $yDepois);
        }
        $this->oPdf->line($this->oPdf->getX(), $this->oPdf->getY(), 203, $this->oPdf->getY());

        $this->oPdf->SetAutoPageBreak(false);
    }

    private function buscarDisciplinasAbreviadas()
    {
        $disciplinasAbreviadas = [];
        foreach ($this->aDadosOrganizados as $etapa) {
            foreach ($etapa->aDisicplinasEtapa as $disciplina) {
                if (strlen($disciplina->sNomeCompleto) >= 65) {
                    $disciplinasAbreviadas[$disciplina->iCadDisciplina] = sprintf(
                        '<b>*%s: %s</b>',
                        $disciplina->sAbrevDisciplina,
                        $disciplina->sNomeCompleto
                    );
                }
            }
        }

        return $disciplinasAbreviadas;
    }

  /**
   * Monta o quadro das informações do certificado
   */
  public function montarQuadroCertificado() {
    $this->oPdf->ln();
    $sWhere  = "     ed61_i_aluno = {$this->oAluno->getCodigoAluno()} ";
    $sWhere .= " and ed61_i_anoconc is not null ";

    $oDaoHistorico = new cl_historico();
    $sSqlCurso     = $oDaoHistorico->sql_query(null, "ed29_c_descr, ed61_i_anoconc", null, $sWhere);
    $rsCurso       = $oDaoHistorico->sql_record($sSqlCurso);

    $oErro = new stdClass();
    $oErro->sNome = $this->oAluno->getNome();

    if ($oDaoHistorico->numrows == 0) {
    	db_redireciona("db_erros.php?fechar=true&db_erro="._M(self::MENSAGEM."aluno_nao_possui_curso_concluido", $oErro));
    }

    $iAlturaDisponivel = $this->oPdf->getAvailHeight();

    if ($iAlturaDisponivel < 30) {
        $this->oPdf->addPage();
    	$this->escreveCabecalho();
        $this->oPdf->ln();
    }

    $oDadosConclusao = db_utils::fieldsMemory($rsCurso, 0);

    $sMsg  = "    Certifico que o(a) aluno(a) {$this->oAluno->getNome()} concluiu {$oDadosConclusao->ed29_c_descr}";
    $sMsg .= " no ano de {$oDadosConclusao->ed61_i_anoconc}, nos termos da Lei 9.394 de 20 de dezembro de 1996, ";
    $sMsg .= "Art. 24, Inciso VII e Regimento Escolar, tendo obtido os resultados constantes neste certificado.";

    $this->oPdf->ln(1);
    $iYAntes = $this->oPdf->GetY();
    $this->oPdf->SetFont("Arial", "", 8);
    $this->oPdf->Cell(195, self::ALTURA_LINHA, "Certificado de Conclusão:", 0, 1, "C");
    $this->oPdf->setMulticellBreakPageFunction( array($this, "escreveCabecalho") );
    $this->oPdf->MultiCell(195, self::ALTURA_LINHA, $sMsg, 0, "J");

    $nLinhas = $this->oPdf->NbLines(195, $sMsg);

    $this->oPdf->Rect($this->oPdf->getLeftMargin(), $iYAntes, 195, self::ALTURA_LINHA + ($nLinhas * self::ALTURA_LINHA));
    return ;
  }

}
