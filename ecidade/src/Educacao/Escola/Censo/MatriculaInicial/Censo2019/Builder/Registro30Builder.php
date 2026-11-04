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

use DBDate;
use DBString;
use ECidade\Educacao\Escola\Censo\Helpers\Pessoa;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro30;
use ECidade\Educacao\Escola\Model\Aluno;
use ECidade\Educacao\Escola\Model\AlunoRecursoNecessarioAvaliacaoInep;
use ECidade\Educacao\Escola\Model\Pais;
use ECidade\Educacao\Escola\Model\ProfissionalEscola;
use Exception;

class Registro30Builder extends BuilderFormulario
{

    /**
     * @var Registro30
     */
    private $registro;

    private $inepEscola;

    /**
     * @var ProfissionalEscola
     */
    private $profissionalEscola;

    /**
     * @var Aluno
     */
    private $alunoEscola;

    /**
     * @var array
     */
    private $necessidadesEspeciais = array();

    private $deParaEscolaridade = array(
        1 => 1,
        2 => 2,
        3 => 7,
        4 => 7,
        5 => 7,
        6 => 6,
    );

    private $deParaRacaAluno = array(
        "NÃO DECLARADA" => 0,
        "BRANCA" => 1,
        "PRETA" => 2,
        "PARDA" => 3,
        "AMARELA" => 4,
        "INDÍGENA" => 5,
    );

    private $outrosDadosFormacao = array();

    /**
     * @var AlunoRecursoNecessarioAvaliacaoInep[]
     */
    private $recursoNecessarioAvaliacaoInep = array();

    /**
     * @param ProfissionalEscola $profissionaisEscola
     */
    public function setDadosProfissional(ProfissionalEscola $profissionaisEscola)
    {
        $this->profissionalEscola = $profissionaisEscola;
    }

    /**
     * @return Registro30
     */
    public function build()
    {
        $this->create();
        if (!empty($this->profissionalEscola)) {
            $this->buildProfissional();
        }

        if (!empty($this->alunoEscola)) {
            $this->buildAluno();
        }

        return $this->registro;
    }

    private function create()
    {
        $this->registro = new Registro30();
    }

    private function buildProfissional()
    {
        $cgm = $this->profissionalEscola->getCgm();
        $this->registro->setCodigoInepEscola($this->profissionalEscola->getEscola()->getCodigoInep());
        $this->registro->setCodigoInep($this->profissionalEscola->getCodigoInep());
        $this->registro->setCodigoPessoa(
            Pessoa::buildCodigoProfissional($this->profissionalEscola->getCgm()->getCpf())
        );
        $this->registro->setCpf($cgm->getCpf());
        $nome = $cgm->getNomeCompleto();
        if (empty($nome)) {
            $nome = $cgm->getNome();
        }
        $nome = DBString::removerAcentuacao($nome);
        $this->registro->setNome(mb_strtoupper($nome));

        $dataNascimento = $cgm->getDataNascimento();
        if (!empty($dataNascimento)) {
            $this->registro->setDataNascimento(DBDate::format($dataNascimento));
        }

        $nomeMae = $cgm->getNomeMae() !== '' ? $cgm->getNomeMae() : null;
        $nomePai = $cgm->getNomePai() !== '' ? $cgm->getNomePai() : null;
        if (empty($nomeMae) && empty($nomePai)) {
            $this->registro->setFiliacao(0);
        } else {
            $this->registro->setFiliacao(1);
            $this->buildFiliacao($nomeMae, $nomePai);
        }
        $this->registro->setSexo($cgm->getSexo() === 'M' ? 1 : 2);
        $this->registro->setCorRaca($this->profissionalEscola->getRaca());
        $this->registro->setNacionalidade($this->profissionalEscola->getNacionalidade());
        $this->registro->setPaisNacionalidade($this->profissionalEscola->getCensoPaisNascimento()->getCodigoOnu());
        $this->registro->setMunicipioNascimento($this->profissionalEscola->getCensoMunicipioNascimento());

        $this->registro->setEscolaridade($this->deParaEscolaridade[$this->profissionalEscola->getEscolaridade()]);
        $this->registro->setTipoEnsinoMedio($this->profissionalEscola->getTipoEnsinoMedio());

        if ($this->profissionalEscola->isGestor()) {
            $this->registro->setEmail($this->profissionalEscola->getGestorEmail());
        } else {
            $this->registro->setPaisResidencia($this->profissionalEscola->getPaisResidencia()->getCodigoOnu());
            $this->registro->setCep($cgm->getCep());
            $this->registro->setMunicipioResidencia($this->profissionalEscola->getMunicipioResidencia());
            $this->registro->setZonaResidencia($this->profissionalEscola->getZonaResidencia());
            $this->registro->setLocalizacaoDiferenciada($this->profissionalEscola->getLocalizacaoDiferenciada());
        }
        $this->buildNecessidadesEspeciais();
        $this->buildFormacao();
        $this->buildPosGraduacao();
        $this->buildOutrosDadosFormacao();
    }

    private function buildNecessidadesEspeciais()
    {
        if (!empty($this->necessidadesEspeciais)) {
            $this->registro->setDeficienciaOuAltismoOuSuperdotacao(1);
            $this->registro->setCegueira(0);
            $this->registro->setbaixaVisao(0);
            $this->registro->setSurdez(0);
            $this->registro->setDeficienciaAuditiva(0);
            $this->registro->setSurdocegueira(0);
            $this->registro->setDeficienciaFisica(0);
            $this->registro->setDeficienciaintelectual(0);
            $this->registro->setDeficienciaMultipla(0);
            $this->registro->setTranstornoAutista(0);
            $this->registro->setSuperdotacao(0);
            $this->registro->setVisaoMonocular(0);
        }

        foreach ($this->necessidadesEspeciais as $necessidade) {
            switch ($necessidade['codigo']) {
                case '101':
                    $this->registro->setCegueira(1);
                    break;
                case '102':
                    $this->registro->setbaixaVisao(1);
                    break;
                case '103':
                    $this->registro->setSurdez(1);
                    break;
                case '104':
                    $this->registro->setDeficienciaAuditiva(1);
                    break;
                case '105':
                    $this->registro->setSurdocegueira(1);
                    break;
                case '106':
                    $this->registro->setDeficienciaFisica(1);
                    break;
                case '107':
                    $this->registro->setDeficienciaintelectual(1);
                    break;
                case '108':
                    $this->registro->setDeficienciaMultipla(1);
                    break;
                case '109':
                    $this->registro->setTranstornoAutista(1);
                    break;
                case '113':
                    $this->registro->setSuperdotacao(1);
                    break;
                case '114':
                    $this->registro->setVisaoMonocular(1);
                    break;
            }
        }

        $campos = array();
        $campos[] = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getVisaoMonocular();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);
        if (isset($campos[1]) && $campos[1] >= 2) {
            $this->registro->setDeficienciaMultipla(1);
        }
    }

    private function buildFormacao()
    {
        if ($this->deParaEscolaridade[$this->profissionalEscola->getEscolaridade()] != 6) {
            return;
        }

        $formacoesComplementar = array();

        foreach ($this->profissionalEscola->getFormacoes() as $index => $formacao) {
            if (count($formacao->getFormacaoComplementar()) > 0) {
                $formacoesComplementar = array_merge($formacoesComplementar, $formacao->getFormacaoComplementar());
            }
            switch ($index) {
                case 0:
                    $this->registro->setNomeCurso1(trim($formacao->getCursoFormacao()->getNome()));
                    $this->registro->setCodigoCurso1(trim($formacao->getCursoFormacao()->getCodigoCenso()));
                    $this->registro->setAnoConclusao1($formacao->getAnoConclusao());
                    $this->registro->setInstituicaoSuperior1($formacao->getCensoInstsuperior()->getCodigo());
                    $this->registro->setAtivo1($formacao->getCursoFormacao()->isAtivo());
                    break;
                case 1:
                    $this->registro->setNomeCurso2(trim($formacao->getCursoFormacao()->getNome()));
                    $this->registro->setCodigoCurso2(trim($formacao->getCursoFormacao()->getCodigoCenso()));
                    $this->registro->setAnoConclusao2($formacao->getAnoConclusao());
                    $this->registro->setInstituicaoSuperior2($formacao->getCensoInstsuperior()->getCodigo());
                    $this->registro->setAtivo2($formacao->getCursoFormacao()->isAtivo());
                    break;
                case 2:
                    $this->registro->setNomeCurso3(trim($formacao->getCursoFormacao()->getNome()));
                    $this->registro->setCodigoCurso3(trim($formacao->getCursoFormacao()->getCodigoCenso()));
                    $this->registro->setAnoConclusao3($formacao->getAnoConclusao());
                    $this->registro->setInstituicaoSuperior3($formacao->getCensoInstsuperior()->getCodigo());
                    $this->registro->setAtivo3($formacao->getCursoFormacao()->isAtivo());
                    break;
            }
        }

        // o array disciplinaInformada é para não deixar informar mais de uma vez a mesma disciplina
        $disciplinaInformada = array();

        foreach ($formacoesComplementar as $index => $formacaoComplementar) {
            if (in_array($formacaoComplementar->getCodigo(), $disciplinaInformada)) {
                continue;
            }
            if ($this->profissionalEscola->isGestor()) {
                if (!$this->profissionalEscola->isDocente()) {
                    continue;
                }
            }
            switch ($index) {
                case 0:
                    $this->registro->setComponenteCurricular1($formacaoComplementar->getCodigo());
                    break;
                case 1:
                    $this->registro->setComponenteCurricular2($formacaoComplementar->getCodigo());
                    break;
                case 2:
                    $this->registro->setComponenteCurricular3($formacaoComplementar->getCodigo());
                    break;
            }

            $disciplinaInformada[] = $formacaoComplementar->getCodigo();
        }
    }

    private function buildOutrosDadosFormacao()
    {
        foreach ($this->outrosDadosFormacao as $grupo => $perguntas) {
            if (array_key_exists('outros_cursos', $perguntas)) {
                $this->buildOutrosCursos($perguntas['outros_cursos']);
            }
        }
    }

    private function buildPosGraduacao()
    {
        $this->registro->setPosGraduacoes($this->profissionalEscola->getPosgraduaceos());
    }

    private function buildOutrosCursos($opcoes)
    {
        $this->registro->setCreche($this->exist('especifico_creche', $opcoes));
        $this->registro->setPreEscola($this->exist('especifico_pre_escola', $opcoes));
        $this->registro->setAnosIniciais($this->exist('especifico_anos_iniciais', $opcoes));
        $this->registro->setAnosFinais($this->exist('especifico_anos_finais', $opcoes));
        $this->registro->setEnsinoMedio($this->exist('especifico_ensino_medio', $opcoes));
        $this->registro->setEja($this->exist('especifico_eja', $opcoes));
        $this->registro->setEducacaoEspecial($this->exist('especifico_educacao_especial', $opcoes));
        $this->registro->setEducacaoIndigena($this->exist('especifico_indigena', $opcoes));
        $this->registro->setEducacaoCampo($this->exist('especifico_campo', $opcoes));
        $this->registro->setEducacaoAmbiental($this->exist('especifico_ambiental', $opcoes));
        $this->registro->setEducacaoDireitosHumanos($this->exist('especifico_direitos_humanos', $opcoes));
        $this->registro->setEducacaoBilingueSurdos($this->exist('educacao_bilingue_surdos', $opcoes));
        $this->registro->setEducacaoTIC($this->exist('educacao_tic', $opcoes));
        $this->registro->setGeneroDiversidadeSexual($this->exist('genero_diversidade_sexual', $opcoes));
        $this->registro->setDireitosCriancaAdolescente($this->exist('direito_crianca_adolescente', $opcoes));
        $this->registro->setEducacaoEtnicoRaciais($this->exist('relacoes_etnicorraciais', $opcoes));
        $this->registro->setGestaoEscolar($this->exist('gestao_escolar', $opcoes));
        $this->registro->setOutros($this->exist('outros_cursos', $opcoes));
        $this->registro->setNenhumCurso($this->exist('nenhum_curso', $opcoes));
    }

    /**
     * @param $inepEscola
     */
    public function setInepEscola($inepEscola)
    {
        $this->inepEscola = $inepEscola;
    }

    public function setNecessidadesEspeciais(array $necessidadesEspeciais)
    {
        $this->necessidadesEspeciais = $necessidadesEspeciais;
    }

    public function setOutrosDadosFormacao(array $outrosDadosAvaliacao)
    {
        $this->outrosDadosFormacao = $outrosDadosAvaliacao;
    }

    /**
     * @param Aluno $aluno
     */
    public function setDadosAluno(Aluno $aluno)
    {
        $this->alunoEscola = $aluno;
    }

    private function buildAluno()
    {
        $cpf = trim($this->alunoEscola->getCpf());
        $nis = trim($this->alunoEscola->getNis());
        $certidaoNasc = $this->alunoEscola->getMatriculaCeridao();
        $this->registro->setCodigoInepEscola($this->inepEscola);
        $this->registro->setCodigoPessoa(Pessoa::buildCodigoAluno($this->alunoEscola->getCodigo()));
        $this->registro->setCodigoInep(trim($this->alunoEscola->getCodigoInep()));
        $this->registro->setCpf($cpf);
        $this->registro->setNome(mb_strtoupper(DBString::removerAcentuacao($this->alunoEscola->getNome())));
        $this->registro->setDataNascimento($this->alunoEscola->getDataNascimento()->getDate(DBDate::DATA_PTBR));
        $this->registro->setFiliacao($this->alunoEscola->getFiliacao());
        $this->buildFiliacao($this->alunoEscola->getMae(), $this->alunoEscola->getPai());
        $this->registro->setSexo($this->alunoEscola->getSexo() == 'M' ? 1 : 2);
        $this->buildRacaAluno($this->alunoEscola->getRaca());
        $this->registro->setNacionalidade($this->alunoEscola->getNacionalidade());
        $this->registro->setMunicipioNascimento($this->alunoEscola->getCensoMunicipioNascimento());
        $this->registro->setPaisNacionalidade($this->alunoEscola->getPais()->getCodigoOnu());
        $tipoCertidao = intval(substr($certidaoNasc, 14, 1));
        if ($tipoCertidao != 2) {
            $this->registro->setCertidaoNascimento($certidaoNasc);
        }
        $paisResidencia = $this->alunoEscola->getPaisResidencia();
        $zona = mb_strtoupper($this->alunoEscola->getZonaResidencia()) == 'URBANA' ? 1 : 2;
        if ($paisResidencia instanceof Pais) {
            $this->registro->setPaisResidencia($paisResidencia->getCodigoOnu());
            if ($paisResidencia->getCodigoOnu() == 76) {
                $this->registro->setZonaResidencia($zona);
                $this->registro->setCep($this->alunoEscola->getCep());
                $this->registro->setMunicipioResidencia($this->alunoEscola->getMunicipioEndereco());
                if (is_null($this->alunoEscola->getMunicipioEndereco())) {
                    $this->registro->setMunicipioResidencia($this->alunoEscola->getCensoMunicipioNascimento());
                }
            }
        }
        $this->registro->setLocalizacaoDiferenciada($this->alunoEscola->getLocalizacaodiferenciada());

        if (empty($cpf) && empty($nis) && empty($certidaoNasc)) {
            $this->registro->setJustificativaFaltaDocumentacao(1);
        }

        $this->buildNecessidadesEspeciais();
        $this->buildRecursoNecessarioAvaliacaoInep();
    }

    private function buildFiliacao($nomeMae, $nomePai)
    {
        $nomeMae = DBString::removerAcentuacao($nomeMae);
        $nomePai = DBString::removerAcentuacao($nomePai);
        if (empty($nomeMae) && !empty($nomePai)) {
            $this->registro->setFiliacao1($nomePai);
        }
        if (!empty($nomeMae) && empty($nomePai)) {
            $this->registro->setFiliacao1($nomeMae);
        }
        if (!empty($nomeMae) && !empty($nomePai)) {
            $this->registro->setFiliacao1($nomeMae);
            $this->registro->setFiliacao2($nomePai);
        }
    }

    private function buildRacaAluno($raca)
    {
        $this->registro->setCorRaca(0);
        if (array_key_exists($raca, $this->deParaRacaAluno)) {
            $this->registro->setCorRaca($this->deParaRacaAluno[$raca]);
        }
    }

    /**
     * @param AlunoRecursoNecessarioAvaliacaoInep[] $recursos
     */
    public function setRecursoNecessarioAvaliacaoInep(array $recursos)
    {
        $this->recursoNecessarioAvaliacaoInep = $recursos;
    }

    private function buildRecursoNecessarioAvaliacaoInep()
    {
        if (empty($this->necessidadesEspeciais)) {
            return;
        }

        $temSuperdotacao = array_filter($this->necessidadesEspeciais, function ($necessidade) {
            if ($necessidade['codigo'] == 113) {
                return true;
            }
        });

        /**
         * Se o campo nessecidades especiais é apenas o 26 ( Altas habilidades / Superdotação)
         * não deve preencher os Recursos
         **/
        if (count($this->necessidadesEspeciais) == 1 && !empty($temSuperdotacao)) {
            return;
        }

        $this->registro->setAuxilioLedor(0);
        $this->registro->setAuxilioTranscricao(0);
        $this->registro->setGuiaInterprete(0);
        $this->registro->setTradutorInterpreteLibras(0);
        $this->registro->setLeituraLabial(0);
        $this->registro->setProvaAmpliada(0);
        $this->registro->setProvaSuperampliada(0);
        $this->registro->setProvaBraille(0);
        $this->registro->setNenhumRecurso(0);
        $this->registro->setAudioDeficienteVisual(0);
        $this->registro->setProvaLinguaPortuguesaSegundaLingua(0);
        $this->registro->setProvaVideoLibras(0);

        foreach ($this->recursoNecessarioAvaliacaoInep as $recuso) {
            switch ($recuso->getCodigoRecursosAvaliacaoInep()) {
                case 101:
                    $this->registro->setAuxilioLedor(1);
                    break;
                case 102:
                    $this->registro->setAuxilioTranscricao(1);
                    break;
                case 103:
                    $this->registro->setGuiaInterprete(1);
                    break;
                case 104:
                    $this->registro->setTradutorInterpreteLibras(1);
                    break;
                case 105:
                    $this->registro->setLeituraLabial(1);
                    break;
                case 107:
                    $this->registro->setProvaAmpliada(1);
                    break;
                case 108:
                    $this->registro->setProvaSuperampliada(1);
                    break;
                case 109:
                    $this->registro->setProvaBraille(1);
                    break;
                case 110:
                    $this->registro->setNenhumRecurso(1);
                    break;
                case 111:
                    $this->registro->setAudioDeficienteVisual(1);
                    break;
                case 112:
                    $this->registro->setProvaLinguaPortuguesaSegundaLingua(1);
                    break;
                case 113:
                    $this->registro->setProvaVideoLibras(1);
                    break;
            }
        }
    }

    /**
     * @param array $linha
     * @return Registro30
     * @throws Exception
     */
    public function buildFromFileLine(array $linha)
    {
        if ($linha[0] != 30) {
            throw new Exception("Linha não é do registro 60");
        }

        $this->create();
        $this->registro->setCodigoInepEscola($linha[1]);
        $this->registro->setCodigoPessoa($linha[2]);
        $this->registro->setCodigoInep($linha[3]);
        $this->registro->setCpf($linha[4]);
        $this->registro->setNome($linha[5]);
        $this->registro->setDataNascimento($linha[6]);
        $this->registro->setFiliacao($linha[7]);
        $this->registro->setFiliacao1($linha[8]);
        $this->registro->setFiliacao2($linha[9]);
        $this->registro->setSexo($linha[10]);
        $this->registro->setCorRaca($linha[11]);
        $this->registro->setNacionalidade($linha[12]);
        $this->registro->setPaisNacionalidade($linha[13]);
        $this->registro->setMunicipioNascimento($linha[14]);
        $this->registro->setDeficienciaOuAltismoOuSuperdotacao($linha[15]);
        $this->registro->setCegueira($linha[16]);
        $this->registro->setBaixaVisao($linha[17]);
        $this->registro->setSurdez($linha[18]);
        $this->registro->setDeficienciaAuditiva($linha[19]);
        $this->registro->setSurdocegueira($linha[20]);
        $this->registro->setDeficienciaFisica($linha[21]);
        $this->registro->setDeficienciaintelectual($linha[22]);
        $this->registro->setDeficienciaMultipla($linha[23]);
        $this->registro->setTranstornoAutista($linha[24]);
        $this->registro->setSuperdotacao($linha[25]);
        $this->registro->setAuxilioLedor($linha[26]);
        $this->registro->setAuxilioTranscricao($linha[27]);
        $this->registro->setGuiaInterprete($linha[28]);
        $this->registro->setTradutorInterpreteLibras($linha[29]);
        $this->registro->setLeituraLabial($linha[30]);
        $this->registro->setProvaAmpliada($linha[31]);
        $this->registro->setProvaSuperampliada($linha[32]);
        $this->registro->setAudioDeficienteVisual($linha[33]);
        $this->registro->setProvaLinguaPortuguesaSegundaLingua($linha[34]);
        $this->registro->setProvaVideoLibras($linha[35]);
        $this->registro->setProvaBraille($linha[36]);
        $this->registro->setNenhumRecurso($linha[37]);
        $this->registro->setCertidaoNascimento($linha[39]);
        $this->registro->setJustificativaFaltaDocumentacao($linha[40]);
        $this->registro->setPaisResidencia($linha[41]);
        $this->registro->setCep($linha[42]);
        $this->registro->setMunicipioResidencia($linha[43]);
        $this->registro->setZonaResidencia($linha[44]);
        $this->registro->setLocalizacaoDiferenciada($linha[45]);
        $this->registro->setEscolaridade($linha[46]);
        $this->registro->setTipoEnsinoMedio($linha[47]);
        $this->registro->setCodigoCurso1($linha[48]);
        $this->registro->setAnoConclusao1($linha[49]);
        $this->registro->setInstituicaoSuperior1($linha[50]);
        $this->registro->setCodigoCurso2($linha[51]);
        $this->registro->setAnoConclusao2($linha[52]);
        $this->registro->setInstituicaoSuperior2($linha[53]);
        $this->registro->setCodigoCurso3($linha[54]);
        $this->registro->setAnoConclusao3($linha[55]);
        $this->registro->setInstituicaoSuperior3($linha[56]);
        $this->registro->setComponenteCurricular1($linha[57]);
        $this->registro->setComponenteCurricular2($linha[58]);
        $this->registro->setComponenteCurricular3($linha[59]);
        $this->registro->setNenhumaPos($linha[63]);
        $this->registro->setCreche($linha[64]);
        $this->registro->setPreEscola($linha[65]);
        $this->registro->setAnosIniciais($linha[66]);
        $this->registro->setAnosFinais($linha[67]);
        $this->registro->setEnsinoMedio($linha[68]);
        $this->registro->setEja($linha[69]);
        $this->registro->setEducacaoEspecial($linha[70]);
        $this->registro->setEducacaoIndigena($linha[71]);
        $this->registro->setEducacaoCampo($linha[72]);
        $this->registro->setEducacaoAmbiental($linha[73]);
        $this->registro->setEducacaoDireitosHumanos($linha[74]);
        $this->registro->setGeneroDiversidadeSexual($linha[75]);
        $this->registro->setDireitosCriancaAdolescente($linha[76]);
        $this->registro->setEducacaoEtnicoRaciais($linha[77]);
        $this->registro->setGestaoEscolar($linha[78]);
        $this->registro->setOutros($linha[79]);
        $this->registro->setNenhumCurso($linha[80]);
        $this->registro->setEmail($linha[81]);
        return $this->registro;
    }
}
