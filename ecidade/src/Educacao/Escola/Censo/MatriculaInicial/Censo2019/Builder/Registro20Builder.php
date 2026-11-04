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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Builder;

use App\Domain\Educacao\Secretaria\Models\UnidadeCurricular;
use ECidade\Educacao\Escola\Censo\Helpers\Turma;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro20;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\TurmaCensoVo;
use ECidade\Enum\Educacao\Escola\FormaOrganizacaoTurmaEum;
use Exception;

class Registro20Builder
{

    /**
     * Modalidade/Tipo da turma na escola e como deve ir para o censo
     * @var array
     */
    private static $deParaModalidadeTurma = array(
        1 => 1,
        2 => 3,
        3 => 1,
        6 => 1,
        7 => 1,
    );

    private static $etapasCursoProfissional = array(30, 31, 32, 33, 34, 39, 40, 64, 74);
    private static $etapasInfantil = array(1, 2, 3);

    /**
     * @var Registro20
     */
    private $registro;

    /**
     * @var TurmaCensoVo
     */
    private $dadosTurma;
    private $diasLetivo = array();

    /**
     * @param TurmaCensoVo $turma
     * @return $this
     */
    public function setDadosTurma(TurmaCensoVo $turma)
    {
        $this->dadosTurma = $turma;
        return $this;
    }

    /**
     * @param array $diasLetivo
     * @return $this
     */
    public function setDiasLetivo(array $diasLetivo)
    {
        $this->diasLetivo = $diasLetivo;
        return $this;
    }

    /**
     * @return Registro20
     */
    public function build()
    {
        $this->create();
        $this->buildTurma();

        return $this->registro;
    }

    private function create()
    {
        $this->registro = new Registro20();
    }

    private function buildTurma()
    {
        $this->registro->setCodigoInepEscola($this->dadosTurma->getEscola()->getCodigoInep());
        $this->registro->setCodigoInep($this->dadosTurma->getCodigoInep());

        $this->registro->setCodigoTurma(Turma::buildCodigoTurmaRegular($this->dadosTurma->getCodigoTurma()));
        if (!$this->dadosTurma->isEscolarizacao()) {
            $this->registro->setCodigoTurma(Turma::buildCodigoTurmaAC($this->dadosTurma->getCodigoTurma()));
        }

        $this->registro->setNomeTurma($this->removerAcentuacao($this->dadosTurma->getNomeTurma()));
        if ($this->dadosTurma->isTurmaUnificada()) {
            $this->registro->setCodigoTurma(
                Turma::buildCodigoTurmaUnificada($this->dadosTurma->getCodigoTurmaUnificada())
            );
        }
        $this->registro->setTipoMediacaoDidaticoPedagogica($this->dadosTurma->getTipoMediacaoDidaticoPedagogica());

        $hora = $this->dadosTurma->getHoraInicio();
        if (!is_null($hora)) {
            $hora = explode(':', $hora);
            $this->registro->setHoraInicio($hora[0]);
            $this->registro->setMinutoInicio($hora[1]);
        }

        $hora = $this->dadosTurma->getHoraFim();
        if (!is_null($hora)) {
            $hora = explode(':', $hora);
            $this->registro->setHoraFim($hora[0]);
            $this->registro->setMinutoFim($hora[1]);
        }
        $this->registro->setEscolarizacao($this->dadosTurma->isEscolarizacao() ? 1 : 0);
        $this->registro->setAtividadeComplementar($this->dadosTurma->isAtividadeComplementar() ? 1 : 0);
        $this->registro->setAtendimentoAEE($this->dadosTurma->isAtendimentoAEE() ? 1 : 0);

        $this->registro->setLocalFuncionamentoDiferenciado($this->dadosTurma->getLocalFuncionamento());
        if ($this->dadosTurma->isEscolarizacao()) {
            $this->registro->setEtapaCenso($this->dadosTurma->getEtapaCenso());
        }
        if (in_array($this->dadosTurma->getEtapaCenso(), self::$etapasCursoProfissional)) {
            $this->registro->setCodigoCurso($this->dadosTurma->getCodigoCurso());
        }
        $this->buildFormaOrganizacaoTurma();
        $this->buildDiasSemana();
        $this->buildAtividadeComplementar();
        $this->buildModalidade();
        $this->buildDisciplinas();
    }

    private function buildDiasSemana()
    {
        if ($this->dadosTurma->getTipoMediacaoDidaticoPedagogica() == 1) {
            $this->registro->setSegundaFeira(0);
            $this->registro->setTercaFeira(0);
            $this->registro->setQuartaFeira(0);
            $this->registro->setQuintaFeira(0);
            $this->registro->setSextaFeira(0);
            $this->registro->setSabado(0);
            $this->registro->setDomingo(0);

            foreach ($this->diasLetivo as $diaSemana) {
                switch (mb_strtoupper($diaSemana)) {
                    case "SEGUNDA":
                        $this->registro->setSegundaFeira(1);
                        break;
                    case "TERÇA":
                        $this->registro->setTercaFeira(1);
                        break;
                    case "QUARTA":
                        $this->registro->setQuartaFeira(1);
                        break;
                    case "QUINTA":
                        $this->registro->setQuintaFeira(1);
                        break;
                    case "SEXTA":
                        $this->registro->setSextaFeira(1);
                        break;
                    case "SABADO":
                        $this->registro->setSabado(1);
                        break;
                    case "DOMINGO":
                        $this->registro->setDomingo(1);
                        break;
                }
            }
        }
    }

    private function buildFormaOrganizacaoTurma()
    {
        $base = $this->dadosTurma->getBase();
        $organizacao = null;
        if (!is_null($base)) {
            $sql = "select ed218_organizacaoturma
                        from regimemat
                    where ed218_i_codigo =
                        (select ed31_i_regimemat
                            from base where ed31_i_codigo = {$base})";
            $rs = db_query($sql);
            if ($rs) {
                $organizacao = pg_fetch_assoc($rs);
                $organizacao = $organizacao['ed218_organizacaoturma'];
            }
        }

        $this->registro->setFromaOrganizaTurma($organizacao);

        if (in_array($this->registro->getEtapaCenso(), [1, 2, 3])) {
            return;
        }

        if ($this->dadosTurma->isAtendimentoAEE() || $this->dadosTurma->isAtividadeComplementar()) {
            return;
        }

        $this->registro->setSerieAno(0);
        $this->registro->setPeriodosSemestrais(0);
        $this->registro->setCiclos(0);
        $this->registro->setGruposNaoSeriados(0);
        $this->registro->setModulos(0);
        $this->registro->setAlternanciaRegular(0);
        $this->registro->setSerieAno(0);
        switch (intval($organizacao)) {
            case FormaOrganizacaoTurmaEum::SERIE_ANO:
                $this->registro->setSerieAno(1);
                if (!in_array($this->registro->getEtapaCenso(), [
                    14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41,
                    25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35,
                    36, 37, 38, 39, 40, 64, 56, 69, 70, 71, 72,
                    73, 74, 67
                ])) {
                    $this->registro->setSerieAno(0);
                }
                break;
            case FormaOrganizacaoTurmaEum::PERIODOS_SEMESTRAIS:
                $this->registro->setPeriodosSemestrais(1);
                if (!in_array($this->registro->getEtapaCenso(), [
                    25, 26, 27, 28, 29, 30, 31, 32, 33, 34,
                    35, 36, 37, 38, 39, 40, 64, 69, 70, 71, 72, 73, 74, 67, 68
                ])) {
                    $this->registro->setPeriodosSemestrais(0);
                }
                break;
            case FormaOrganizacaoTurmaEum::CICLOS:
                $this->registro->setCiclos(1);
                if (!in_array($this->registro->getEtapaCenso(), [
                    14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 56
                ])) {
                    $this->registro->setCiclos(0);
                }
                break;
            case FormaOrganizacaoTurmaEum::GRUPOS_NAO_SERIADOS:
                $this->registro->setGruposNaoSeriados(1);
                if (!in_array($this->registro->getEtapaCenso(), [
                    14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31,
                    32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 56, 69, 70, 71, 72, 73, 74, 67, 68
                ])) {
                    $this->registro->setGruposNaoSeriados(0);
                }
                break;
            case FormaOrganizacaoTurmaEum::MODULOS:
                $this->registro->setModulos(1);
                if (!in_array($this->registro->getEtapaCenso(), [
                    14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31,
                    32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 56, 69, 70, 71, 72, 73, 74, 67, 68
                ])) {
                    $this->registro->setModulos(0);
                }
                break;
            case FormaOrganizacaoTurmaEum::ALTERNANCIA_REGULAR:
                $this->registro->setAlternanciaRegular(1);
                if (!in_array($this->registro->getEtapaCenso(), [
                    19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36,
                    37, 38, 39, 40, 64, 69, 70, 71, 72, 73, 74, 67, 68
                ])) {
                    $this->registro->setAlternanciaRegular(0);
                }
                break;
        }
    }

    private function buildAtividadeComplementar()
    {
        if (!$this->dadosTurma->isAtividadeComplementar()) {
            return;
        }
        $atividades = $this->dadosTurma->getAtividadesComplementar();
        foreach ($atividades as $i => $atividade) {
            switch ($i) {
                case 0:
                    $this->registro->setCodigo1($atividade);
                    break;
                case 1:
                    $this->registro->setCodigo2($atividade);
                    break;
                case 2:
                    $this->registro->setCodigo3($atividade);
                    break;
                case 3:
                    $this->registro->setCodigo4($atividade);
                    break;
                case 4:
                    $this->registro->setCodigo5($atividade);
                    break;
                case 5:
                    $this->registro->setCodigo6($atividade);
                    break;
            }
        }
    }

    private function buildModalidade()
    {
        if (!$this->dadosTurma->isEscolarizacao()) {
            return;
        }

        $tipoTurma = $this->dadosTurma->getTipoTurma();
        if (array_key_exists($tipoTurma, self::$deParaModalidadeTurma)) {
            $this->registro->setModalidade(self::$deParaModalidadeTurma[$tipoTurma]);
        }
        $codigoCurso = $this->dadosTurma->getCodigoCurso();
        if (!empty($codigoCurso)) {
            $this->registro->setModalidade(4);
        }
    }

    private function buildDisciplinas()
    {
        if (!$this->dadosTurma->isEscolarizacao() && ($this->dadosTurma->isAtendimentoAEE()
                || $this->dadosTurma->isAtividadeComplementar())) {
                    $this->registro->nulaProjetoVida();
            return;
        }
        $etapasPorEnsino = $this->dadosTurma->getEtapasPorEnsino();
        if (in_array($this->dadosTurma->getEtapaCenso(), $etapasPorEnsino['EI'])) {
            $this->registro->setTipoEstruturaCurricular($this->dadosTurma->getTipoEstruturaCurricular());
            $this->registro->setFormacaoGeralBasica();
            $this->registro->setItinerarioFormativo();
            $this->registro->setNaoAplica();
            $this->registro->nulaProjetoVida();
            return;
        }
        if ($this->dadosTurma->isAtendimentoAEE()) {
            $this->registro->nulaProjetoVida();
            $this->dadosTurma->setTipoEstruturaCurricular();
            $this->registro->setTipoEstruturaCurricular($this->dadosTurma->getTipoEstruturaCurricular());
        }

        $this->registro->setQuimica(0);
        $this->registro->setFisica(0);
        $this->registro->setMatematica(0);
        $this->registro->setBiologia(0);
        $this->registro->setCiencias(0);
        $this->registro->setLiteraturaPortuguesa(0);
        $this->registro->setLiteraturaEstrangeiraIngles(0);
        $this->registro->setLiteraturaEstrangeiraEspanhol(0);
        $this->registro->setLiteraturaEstrangeiraOutra(0);
        $this->registro->setArtes(0);
        $this->registro->setEducacaoFisica(0);
        $this->registro->setHistoria(0);
        $this->registro->setGeografia(0);
        $this->registro->setFilosofia(0);
        $this->registro->setInformatica(0);
        $this->registro->setCursosTecnicosProfissionais(0);
        $this->registro->setLibras(0);
        $this->registro->setDisciplinasPedagogicas(0);
        $this->registro->setEnsinoReligioso(0);
        $this->registro->setLinguaIndigena(0);
        $this->registro->setEstudosSociais(0);
        $this->registro->setSociologia(0);
        $this->registro->setLiteraturaEstrangeiraFrances(0);
        $this->registro->setPortuguesComoSegundaLingua(0);
        $this->registro->setEstagioSupervisionado(0);
        $this->registro->setOutrasDisciplinas(0);

        $disciplinas = $this->dadosTurma->getDisciplinas();
        foreach ($disciplinas as $disciplina) {
            $estruturaCurricular = $disciplina->getTipoBase()['ed182_estrutura_curricular'];
            if (!in_array($estruturaCurricular, $this->dadosTurma->getTipoEstruturaCurricular())) {
                $this->dadosTurma->setTipoEstruturaCurricular($estruturaCurricular);
            }

            switch ($disciplina->getDisciplina()->getCodigo()) {
                case 1:
                    $this->registro->setQuimica(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setQuimica(1);
                    }
                    break;
                case 2:
                    $this->registro->setFisica(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setFisica(1);
                    }
                    break;
                case 3:
                    $this->registro->setMatematica(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setMatematica(1);
                    }
                    break;
                case 4:
                    $this->registro->setBiologia(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setBiologia(1);
                    }
                    break;
                case 5:
                    $this->registro->setCiencias(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setCiencias(1);
                    }
                    break;
                case 6:
                    $this->registro->setLiteraturaPortuguesa(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setLiteraturaPortuguesa(1);
                    }
                    break;
                case 7:
                    $this->registro->setLiteraturaEstrangeiraIngles(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setLiteraturaEstrangeiraIngles(1);
                    }
                    break;
                case 8:
                    $this->registro->setLiteraturaEstrangeiraEspanhol(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setLiteraturaEstrangeiraEspanhol(1);
                    }
                    break;
                case 9:
                    $this->registro->setLiteraturaEstrangeiraOutra(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setLiteraturaEstrangeiraOutra(1);
                    }
                    break;
                case 10:
                    $this->registro->setArtes(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setArtes(1);
                    }
                    break;
                case 11:
                    $this->registro->setEducacaoFisica(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setEducacaoFisica(1);
                    }
                    break;
                case 12:
                    $this->registro->setHistoria(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setHistoria(1);
                    }
                    break;
                case 13:
                    $this->registro->setGeografia(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setGeografia(1);
                    }
                    break;
                case 14:
                    $this->registro->setFilosofia(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setFilosofia(1);
                    }
                    break;
                case 16:
                    $this->registro->setInformatica(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setInformatica(1);
                    }
                    break;
                case 17:
                    $this->registro->setCursosTecnicosProfissionais(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setCursosTecnicosProfissionais(1);
                    }
                    break;
                case 23:
                    $this->registro->setLibras(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setLibras(1);
                    }
                    break;
                case 25:
                    $this->registro->setDisciplinasPedagogicas(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setDisciplinasPedagogicas(1);
                    }
                    break;
                case 26:
                    $this->registro->setEnsinoReligioso(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setEnsinoReligioso(1);
                    }
                    break;
                case 27:
                    $this->registro->setLinguaIndigena(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setLinguaIndigena(1);
                    }
                    break;
                case 28:
                    $this->registro->setEstudosSociais(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setEstudosSociais(1);
                    }
                    break;
                case 29:
                    $this->registro->setSociologia(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setSociologia(1);
                    }
                    break;
                case 30:
                    $this->registro->setLiteraturaEstrangeiraFrances(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setLiteraturaEstrangeiraFrances(1);
                    }
                    break;
                case 31:
                    $this->registro->setPortuguesComoSegundaLingua(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setPortuguesComoSegundaLingua(1);
                    }
                    break;
                case 32:
                    $this->registro->setEstagioSupervisionado(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setEstagioSupervisionado(1);
                    }
                    break;
                case 99:
                    $this->registro->setOutrasDisciplinas(2);
                    if ($disciplina->isOferece()) {
                        $this->registro->setOutrasDisciplinas(1);
                    }
                    break;
            }
        }

        $this->registro->setTipoEstruturaCurricular($this->dadosTurma->getTipoEstruturaCurricular());
        $this->registro->setFormacaoGeralBasica();
        $this->registro->setItinerarioFormativo();
        $this->registro->setNaoAplica();

        foreach ($disciplinas as $disciplina) {
            if ($this->registro->getItinerarioFormativo() === 1 && !is_null($disciplina->getUnidadeCurricular())) {
                $outrasUnidade = [];
                $this->registro->setEletivas(0);
                $this->registro->setLibras(0);
                $this->registro->setLinguaIndigenaUnidade(0);
                $this->registro->setLinguaEspanhol(0);
                $this->registro->setLinguaFrances(0);
                $this->registro->setLinguaOutra(0);
                $this->registro->setProjetoVidaUnidade(0);
                $this->registro->setTrilhaAprofundamento(0);
                switch ($disciplina->getUnidadeCurricular()) {
                    case 1:
                        $this->registro->setEletivas(1);
                        break;
                    case 2:
                        $this->registro->setLibras(1);
                        break;
                    case 3:
                        $this->registro->setLinguaIndigenaUnidade(1);
                        break;
                    case 4:
                        $this->registro->setLinguaEspanhol(1);
                        break;
                    case 5:
                        $this->registro->setLinguaFrances(1);
                        break;
                    case 6:
                        $this->registro->setLinguaOutra(1);
                        break;
                    case 7:
                        $this->registro->setProjetoVidaUnidade(1);
                        break;
                    case 8:
                        $this->registro->setTrilhaAprofundamento(1);
                        break;
                    default:
                        $outrasUnidades[] =
                            UnidadeCurricular::find($disciplina->getUnidadeCurricular())->ed199_descricao;
                }

                $outrasUnidades = count($outrasUnidade) === 0 ? 0 : implode(";", $outrasUnidades);
                $this->registro->setOutrasUnidades($outrasUnidades);
            }
        }
    }

    private function removerAcentuacao($sString)
    {

        $sString = str_replace(chr(176), chr(186), $sString);
        $sString = str_replace('', '', $sString);
        $sString = preg_replace("/[ÁÀÂÃ]/", "A", $sString);
        $sString = preg_replace("/[áàâã]/", "a", $sString);

        $sString = preg_replace("/[ÉÈÊ]/", "E", $sString);
        $sString = preg_replace("/[éèê]/", "e", $sString);

        $sString = preg_replace("/[ÓÒÔÕÖ]/", "O", $sString);
        $sString = preg_replace("/[óòôõö]/", "o", $sString);

        $sString = preg_replace("/[ÚÙÛÜ]/", "U", $sString);
        $sString = preg_replace("/[úùûü]/", "u", $sString);

        $sString = preg_replace("/[ÍÌÎ]/", "I", $sString);
        $sString = preg_replace("/[íìî]/", "i", $sString);

        $sString = preg_replace("/Ç/", "C", $sString);
        $sString = preg_replace("/ç/", "c", $sString);

        return $sString;
    }

    /**
     * @param array $linha
     * @return Registro20
     * @throws Exception
     */
    public function buildFromFileLine(array $linha)
    {
        if ($linha[0] != 20) {
            throw new Exception("Linha não é do registro 60");
        }

        $this->create();
        $this->registro->setCodigoInepEscola($linha[1]);
        $this->registro->setCodigoTurma($linha[2]);
        $this->registro->setCodigoInep($linha[3]);
        $this->registro->setNomeTurma($linha[4]);
        $this->registro->setTipoMediacaoDidaticoPedagogica($linha[5]);
        $this->registro->setHoraInicio($linha[6]);
        $this->registro->setMinutoInicio($linha[7]);
        $this->registro->setHoraFim($linha[8]);
        $this->registro->setMinutoFim($linha[9]);
        $this->registro->setDomingo($linha[10]);
        $this->registro->setSegundaFeira($linha[11]);
        $this->registro->setTercaFeira($linha[12]);
        $this->registro->setQuartaFeira($linha[13]);
        $this->registro->setQuintaFeira($linha[14]);
        $this->registro->setSextaFeira($linha[15]);
        $this->registro->setSabado($linha[16]);
        $this->registro->setEscolarizacao($linha[17]);
        $this->registro->setAtividadeComplementar($linha[18]);
        $this->registro->setAtendimentoAEE($linha[19]);
        $this->registro->setCodigo1($linha[20]);
        $this->registro->setCodigo2($linha[21]);
        $this->registro->setCodigo3($linha[22]);
        $this->registro->setCodigo4($linha[23]);
        $this->registro->setCodigo5($linha[24]);
        $this->registro->setCodigo6($linha[25]);
        $this->registro->setLocalFuncionamentoDiferenciado($linha[26]);
        $this->registro->setModalidade($linha[27]);
        $this->registro->setEtapaCenso($linha[28]);
        $this->registro->setCodigoCurso($linha[29]);
        $this->registro->setQuimica($linha[30]);
        $this->registro->setFisica($linha[31]);
        $this->registro->setMatematica($linha[32]);
        $this->registro->setBiologia($linha[33]);
        $this->registro->setCiencias($linha[34]);
        $this->registro->setLiteraturaPortuguesa($linha[35]);
        $this->registro->setLiteraturaEstrangeiraIngles($linha[36]);
        $this->registro->setLiteraturaEstrangeiraEspanhol($linha[37]);
        $this->registro->setLiteraturaEstrangeiraOutra($linha[38]);
        $this->registro->setArtes($linha[39]);
        $this->registro->setEducacaoFisica($linha[40]);
        $this->registro->setHistoria($linha[41]);
        $this->registro->setGeografia($linha[42]);
        $this->registro->setFilosofia($linha[43]);
        $this->registro->setInformatica($linha[44]);
        $this->registro->setCursosTecnicosProfissionais($linha[45]);
        $this->registro->setLibras($linha[46]);
        $this->registro->setDisciplinasPedagogicas($linha[47]);
        $this->registro->setEnsinoReligioso($linha[48]);
        $this->registro->setLinguaIndigena($linha[49]);
        $this->registro->setEstudosSociais($linha[5]);
        $this->registro->setSociologia($linha[51]);
        $this->registro->setLiteraturaEstrangeiraFrances($linha[52]);
        $this->registro->setPortuguesComoSegundaLingua($linha[53]);
        $this->registro->setEstagioSupervisionado($linha[54]);
        $this->registro->setOutrasDisciplinas($linha[55]);

        return $this->registro;
    }
}
