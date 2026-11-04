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

namespace ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Validators;

use DBString;
use DBDate;
use ECidade\Educacao\Escola\Censo\Log\LogMatriculaInicial;
use ECidade\Educacao\Escola\Censo\Registry\LogCensoRegistry;
use Exception;
use ECidade\Educacao\Escola\Censo\Censo;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro00;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro20;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro30;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro40;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro50;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro60;
use ECidade\Educacao\Escola\Censo\Helpers\Pessoa;

class Registro30Validator
{
    /**
     * @var Registro30
     */
    private $registro;

    /**
     * @var Registro00
     */
    private $registro00;

    /**
     * @var Registro20[]
     */
    private $registros20;

    /**
     * @var Registro40
     */
    private $registro40;

    /**
     * @var Registro50[]
     */
    private $registros50;

    /**
     * @var Registro60
     */
    private $registro60;

    /**
     * @var Censo
     */
    private $censo;

    public function setRegistro(Registro30 $registro)
    {
        $this->registro = $registro;
    }

    public function setRegistro00(Registro00 $registro00)
    {
        $this->registro00 = $registro00;
    }

    public function setRegistros20(array $registros20)
    {
        $this->registros20 = $registros20;
    }

    public function setRegistro40(Registro40 $registro40 = null)
    {
        $this->registro40 = $registro40;
    }

    public function setRegistros50(array $registros50 = null)
    {
        $this->registros50 = $registros50;
    }

    public function setRegistro60(Registro60 $registro60 = null)
    {
        $this->registro60 = $registro60;
    }

    public function setCenso(Censo $censo)
    {
        $this->censo = $censo;
    }

    /**
     * @param $dado
     * @throws Exception
     */
    public function log($dado)
    {
        $tipo = "Servidor";
        if (strpos($this->registro->getCodigoPessoa(), (string)Pessoa::CODIGO_ALUNO) === 0) {
            $tipo = "Aluno";
        }

        $dado = "{$tipo} - {$this->registro->getNome()}: $dado";

        $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
        $log->add(LogMatriculaInicial::REGISTRO30, $dado);
    }

    /**
     * Diz todos o campo foi preenchido
     * @return boolean
     */
    private function isPreenchido($campo)
    {
        return $campo !== "" && !is_null($campo);
    }

    /**
     * Diz todos os campos foram preenchidos
     * @return boolean
     */
    private function isPreenchidoMany(array $campos)
    {
        foreach ($campos as $campo) {
            if (!$this->isPreenchido($campo)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Diz se todas as turmas vinculadas estão em alguma das etapas informadas
     */
    private function turmaEtapaAny(array $etapas)
    {
        foreach ($this->registros20 as $reg20) {
            if (!in_array($reg20->getEtapaCenso(), $etapas)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Diz se todas as turmas vinculadas pertencem a alguma modalidade
     */
    private function turmaModalidadeAny(array $modalidades)
    {
        foreach ($this->registros20 as $reg20) {
            if (!in_array($reg20->getModalidade(), $modalidades)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Diz se alguma turma tem atendimentoAee == $atendimento
     */
    private function turmaAtendimentoAee($atendimento)
    {
        foreach ($this->registros20 as $reg20) {
            if ($reg20->getAtendimentoAEE() == $atendimento) {
                return true;
            }
        }
        return false;
    }

    public function validar()
    {
        if ($this->validarCodigoPessoa()) {
            return;
        }

        $this->validarTipoRegistro();
        $this->validarCodigoInepEscola();
        $this->validarCodigoInep();
        $this->validarCpf();
        $this->validarNome();
        $this->validarDataNascimento();
        $this->validarFiliacao();
        $this->validarFiliacao1();
        $this->validarFiliacao2();
        $this->validarSexo();
        $this->validarCorRaca();
        $this->validarNacionalidade();
        $this->validarPaisNacionalidade();
        $this->validarMunicipioNascimento();
        $this->validarDeficienciaOuAltismoOuSuperdotacao();
        $this->validarDeficiencia();
        $this->validarCegueira();
        $this->validarVisaoMonocular();
        $this->validarBaixaVisao();
        $this->validarSurdez();
        $this->validarDeficienciaAuditiva();
        $this->validarSurdocegueira();
        $this->validarDeficienciaFisica();
        $this->validarDeficienciaintelectual();
        $this->validarDeficienciaMultipla();
        $this->validarTranstornoAutista();
        $this->validarSuperdotacao();
        $this->validarRecursosNecessarios();
        $this->validarAuxilioLedor();
        $this->validarAuxilioTranscricao();
        $this->validarGuiaInterprete();
        $this->validarTradutorInterpreteLibras();
        $this->validarLeituraLabial();
        $this->validarProvaAmpliada();
        $this->validarProvaSuperampliada();
        $this->validarAudioDeficienteVisual();
        $this->validarProvaLinguaPortuguesaSegundaLingua();
        $this->validarProvaVideoLibras();
        $this->validarProvaBraille();
        $this->validarNenhumRecurso();
//        $this->validarNis();
        $this->validarCertidaoNascimento();
//        $this->validarJustificativaFaltaDocumentacao();
        $this->validarPaisResidencia();
        $this->validarCep();
        $this->validarMunicipioResidencia();
        $this->validarZonaResidencia();
        $this->validarLocalizacaoDiferenciada();
        $this->validarEscolaridade();
        $this->validarTipoEnsinoMedio();
        $this->validarCodigoCurso1();
        $this->validarAnoConclusao1();
        $this->validarInstituicaoSuperior1();
        $this->validarCodigoCurso2();
        $this->validarAnoConclusao2();
        $this->validarInstituicaoSuperior2();
        $this->validarCodigoCurso3();
        $this->validarAnoConclusao3();
        $this->validarInstituicaoSuperior3();
        $this->validarComponenteCurricular1();
        $this->validarComponenteCurricular2();
        $this->validarComponenteCurricular3();
        $this->validarPosGraduacoes();
        $this->validarNenhumaPos();
        $this->validarOutrosCursos();
        $this->validarCreche();
        $this->validarPreEscola();
        $this->validarAnosIniciais();
        $this->validarAnosFinais();
        $this->validarEnsinoMedio();
        $this->validarEja();
        $this->validarEducacaoEspecial();
        $this->validarEducacaoIndigena();
        $this->validarEducacaoCampo();
        $this->validarEducacaoAmbiental();
        $this->validarEducacaoDireitosHumanos();
        $this->validarEducacaoBilingueSurdos();
        $this->validarEducacaoTIC();
        $this->validarGeneroDiversidadeSexual();
        $this->validarDireitosCriancaAdolescente();
        $this->validarEducacaoEtnicoRaciais();
        $this->validarGestaoEscolar();
        $this->validarOutros();
        $this->validarNenhumCurso();
        $this->validarEmail();
    }

    private function validarTipoRegistro()
    {
        if ($this->registro->getTipoRegistro() !== '30') {
            $this->log('O tipo de registro deve ser "30".');
        }
    }

    private function validarCodigoInepEscola()
    {
        $inep = $this->registro->getCodigoInepEscola();
        $inepAnterior = $this->registro00->getCodigoInep();
        $campo = 'Código de escola - Inep';

        if (!$this->isPreenchido($inep)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($inep != $inepAnterior) {
            $this->log(sprintf('O campo "%s" está diferente do registro 00 antecedente.', $campo));
        }
    }

    private function validarCodigoPessoa()
    {
        $dado = $this->registro->getCodigoPessoa();
        $campo = 'Código da pessoa física no sistema próprio';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (strlen($dado) > 20) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (empty($this->registro40) && empty($this->registros50) && empty($this->registro60)) {
            $this->log('A pessoa física informada não possui nenhum vínculo na escola.');
            return true;
        }

        // Validacao da regra 4 do campo 3 (pessoa fisica informada duas vezes na mesma escola)
        // garantida pela verificacao de integridade do sistema
    }

    private function validarCodigoInep()
    {
        $dado = $this->registro->getCodigoInep();
        $campo = 'Identificação única (Inep)';

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) != 12) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($dado)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        // Validacao regra 6 do campo 4 (pessoa fisica informada duas vezes na mesma escola)
        // garantida pela verificacao de integridade do sistema
    }

    private function validarCpf()
    {
        $dado = $this->registro->getCpf();
        $nacionalidade = $this->registro->getNacionalidade();
        $campo = 'Número do CPF';
        if (!empty($this->registro60) &&
            !$this->isPreenchido($dado) && $this->turmaEtapaAny([69, 70, 72, 71, 67, 73, 74])) {
            $this->log("CPF ALUNO - A pessoa física foi importada,
            mas está faltando o número do CPF que deverá ser informado na hora do fechamento da escola.");
            return;
        }

        if (!$this->isPreenchido($dado) && in_array($nacionalidade, array(1, 2)) && !empty($this->registro40)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($dado) && in_array($nacionalidade, array(1, 2)) && !empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) != 11) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($dado)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarNome()
    {
        $dado = $this->registro->getNome();

        $aNome = explode(" ", $dado);
        $cpf = $this->registro->getCpf();

        if (!$this->isPreenchido($cpf)) {
            if (strlen($aNome[0]) == 1 && (isset($aNome[1]) && strlen($aNome[1]) == 1)) {
                $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            }
        }

        $campo = 'Nome completo';
        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarDataNascimento()
    {
        $dado = $this->registro->getDataNascimento();
        $campo = 'Data de nascimento';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        list($dia, $mes, $ano) = sscanf($dado, '%d/%d/%d');
        if (!checkdate($mes, $dia, $ano)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $data = DBDate::create($dado);
        $dataCenso = $this->censo->getDataCenso();

        if ($data->getTimeStamp() > $dataCenso->getTimeStamp()) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        // TODO: Validar regras 5-39 do campo 7
    }

    private function validarFiliacao()
    {
        $dado = $this->registro->getFiliacao();
        $filiacao1 = $this->registro->getFiliacao1();
        $filiacao2 = $this->registro->getFiliacao2();
        $campo = 'Filiação';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && !($this->isPreenchido($filiacao1) || $this->isPreenchido($filiacao2))) {
            $this->log(sprintf(
                'O campo "Filiação 1" ou o campo "Filiação 2" deve ser preenchido quando o campo "%s" for '.
                'preenchido com "Filiação 1 e/ou Filiação 2".',
                $campo
            ));
        }
    }

    private function validarFiliacao1()
    {
        $dado = $this->registro->getFiliacao1();
        $filiacao = $this->registro->getFiliacao();
        $campo = 'Filiação 1 (preferencialmente o nome da mãe)';

        if ($this->isPreenchido($dado) && $filiacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        $aNome = explode(" ", $dado);
        $cpf = $this->registro->getCpf();
        if (!$this->isPreenchido($cpf)) {
            if (strlen($aNome[0]) == 1 && (isset($aNome[1]) && strlen($aNome[1]) == 1)) {
                $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            }
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) > 100) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }


        if (!DBString::isSomenteLetras(str_replace(' ', '', $dado))) {
            $this->log(sprintf('O campo "%s" contém caracteres inválidos.', $campo));
        }
    }

    private function validarFiliacao2()
    {
        $dado = $this->registro->getFiliacao2();
        $filiacao = $this->registro->getFiliacao();
        $filiacao1 = $this->registro->getFiliacao1();
        $campo = 'Filiação 2 (preferencialmente o nome do pai)';

        $aNome = explode(" ", $dado);
        $cpf = $this->registro->getCpf();


        if (strlen($aNome[0]) == 1 && (isset($aNome[1]) && strlen($aNome[1]) == 1)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }


        if ($this->isPreenchido($dado) && $filiacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) > 100) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteLetras(str_replace(' ', '', $dado))) {
            $this->log(sprintf('O campo "%s" contém caracteres inválidos.', $campo));
        }

        if (str_word_count($dado) <= 1) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        // TODO: Implementar regra 5 do campo 10?

        if ($dado == $filiacao1) {
            $this->log(sprintf('O campo "%s" não pode ser igual ao campo "Filiação 1".', $campo));
        }
    }

    private function validarSexo()
    {
        $dado = $this->registro->getSexo();
        $campo = 'Sexo';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($dado, array(1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCorRaca()
    {
        $dado = $this->registro->getCorRaca();
        $campo = 'Cor/Raça';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($dado, array(0, 1, 2, 3, 4, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarNacionalidade()
    {
        $dado = $this->registro->getNacionalidade();
        $campo = 'Nacionalidade';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($dado, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarPaisNacionalidade()
    {
        $dado = $this->registro->getPaisNacionalidade();
        $nacionalidade = $this->registro->getNacionalidade();
        $campo = 'País de nacionalidade';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($dado != 76 && in_array($nacionalidade, array(1, 2))) {
            $this->log(sprintf(
                'O campo "%s" deve ser preenchido com 76 quando o campo "Nacionalidade" for preenchido com '.
                '"Brasileira" ou "Brasileira - nascido no Exterior ou Naturalizado".',
                $campo
            ));
        }

        if ($dado == 76 && $nacionalidade == 3) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com 76 quando o campo "Nacionalidade" for preenchido com'.
                ' "Estrangeira".',
                $campo
            ));
        }
    }

    private function validarMunicipioNascimento()
    {
        $dado = $this->registro->getMunicipioNascimento();
        $nacionalidade = $this->registro->getNacionalidade();
        $campo = 'Município de nascimento';

        if (!$this->isPreenchido($dado) && $nacionalidade == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $nacionalidade != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado)) {
            return;
        }
    }

    private function validarDeficienciaOuAltismoOuSuperdotacao()
    {
        $dado = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $campo = 'Pessoa física com deficiência, transtorno do espectro autista ou altas habilidades/superdotação';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado != 1 && !empty($this->registro60) && $this->turmaModalidadeAny(array(2))) {
            $this->log(
                'A pessoa física tem vínculo de aluno(a) em turma de educação especial mas informou que '.
                'não possui deficiência, transtorno do espectro autista ou altas habilidades/ superdotação'
            );
        }

        if ($dado != 1 && !empty($this->registro60) && $this->turmaAtendimentoAee(1)) {
            $this->log(
                'A pessoa física tem vínculo de aluno(a) em turma de AEE mas informou que não possui '.
                'deficiência, transtorno do espectro autista ou altas habilidades/ superdotação.'
            );
        }
    }

    private function validarDeficiencia()
    {
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();

        $campos = array();
        $campos[] = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos[] = $this->registro->getSuperdotacao();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) && $deficiencia == 1 && $campos[1] < 1) {
            $this->log(
                '"Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação" '.
                'não foi preenchido corretamente. Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    public function validarVisaoMonocular()
    {
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $dado = $this->registro->getVisaoMonocular();
        $surdocegueira = $this->registro->getSurdocegueira();
        $campo = 'Visão monocular';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $surdocegueira == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Surdocegueira".', $campo));
        }
    }

    private function validarCegueira()
    {
        $dado = $this->registro->getCegueira();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $visaoBaixa = $this->registro->getBaixaVisao();
        $surdez = $this->registro->getSurdez();
        $surdocegueira = $this->registro->getSurdocegueira();
        $visaoMonocular = $this->registro->getVisaoMonocular();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - Cegueira';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $visaoBaixa == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Baixa visão".', $campo));
        }

        if ($dado == 1 && $visaoBaixa == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Visão Monocular".', $campo));
        }
        if ($dado == 1 && $surdez == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Surdez".', $campo));
        }

        if ($dado == 1 && $surdocegueira == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Surdocegueira".', $campo));
        }
    }

    private function validarBaixaVisao()
    {
        $dado = $this->registro->getBaixaVisao();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $surdocegueira = $this->registro->getSurdocegueira();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - Baixa visão';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $surdocegueira == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Surdocegueira".', $campo));
        }
    }

    private function validarSurdez()
    {
        $dado = $this->registro->getSurdez();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $deficienciaAuditiva = $this->registro->getDeficienciaAuditiva();
        $surdocegueira = $this->registro->getSurdocegueira();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - Surdez';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $deficienciaAuditiva == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Deficiência auditiva".', $campo));
        }

        if ($dado == 1 && $surdocegueira == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Surdocegueira".', $campo));
        }
    }

    private function validarDeficienciaAuditiva()
    {
        $dado = $this->registro->getDeficienciaAuditiva();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $surdocegueira = $this->registro->getSurdocegueira();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - ';
        $campo .= 'Deficiência auditiva';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }


        if ($dado == 1 && $surdocegueira == 1) {
            $this->log(sprintf('O campo "%s" incompatível com o campo "Surdocegueira".', $campo));
        }
    }

    private function validarSurdocegueira()
    {
        $dado = $this->registro->getSurdocegueira();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - Surdocegueira';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarDeficienciaFisica()
    {
        $dado = $this->registro->getDeficienciaFisica();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - ';
        $campo .= 'Deficiência física';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarDeficienciaintelectual()
    {
        $dado = $this->registro->getDeficienciaintelectual();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - ';
        $campo .= 'Deficiência intelectual';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarDeficienciaMultipla()
    {
        $dado = $this->registro->getDeficienciaMultipla();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - ';
        $campo .= 'Deficiência múltipla';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $campos = array();
        $campos[] = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) && $campos[1] >= 2 && $dado != 1) {
            $this->log(sprintf(
                'O campo "%s" não foi preenchido com "Sim", mas as deficiências combinadas acarretam em '.
                'deficiência múltipla.',
                $campo
            ));
        }

        if (isset($campos[1]) && $campos[1] < 2 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" foi preenchido com "Sim", mas as deficiências informadas não acarretam em '.
                'deficiência múltipla.',
                $campo
            ));
        }
    }

    private function validarTranstornoAutista()
    {
        $dado = $this->registro->getTranstornoAutista();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - ';
        $campo .= 'Transtorno do espectro autista';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarSuperdotacao()
    {
        $dado = $this->registro->getSuperdotacao();
        $deficiencia = $this->registro->getDeficienciaOuAltismoOuSuperdotacao();
        $campo = 'Tipo de deficiência, transtorno do espectro autista e altas habilidades/superdotação - ';
        $campo .= 'Altas habilidades/ superdotação';

        if (!$this->isPreenchido($dado) && $deficiencia == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $deficiencia != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarRecursosNecessarios()
    {
        $deficiencias = array();
        $deficiencias[] = $cegueira = $this->registro->getCegueira();
        $deficiencias[] = $this->registro->getBaixaVisao();
        $deficiencias[] = $this->registro->getSurdez();
        $deficiencias[] = $this->registro->getDeficienciaAuditiva();
        $deficiencias[] = $surdocegueira = $this->registro->getSurdocegueira();
        $deficiencias[] = $this->registro->getDeficienciaFisica();
        $deficiencias[] = $this->registro->getDeficienciaintelectual();
        $deficiencias[] = $this->registro->getDeficienciaMultipla();
        $deficiencias[] = $this->registro->getTranstornoAutista();
        $deficiencias = array_diff($deficiencias, array(null));
        $deficiencias = array_count_values($deficiencias);

        $recursos = array();
        $recursos[] = $this->registro->getAuxilioLedor();
        $recursos[] = $auxilioTranscricao = $this->registro->getAuxilioTranscricao();
        $recursos[] = $this->registro->getGuiaInterprete();
        $recursos[] = $this->registro->getTradutorInterpreteLibras();
        $recursos[] = $this->registro->getLeituraLabial();
        $recursos[] = $this->registro->getProvaAmpliada();
        $recursos[] = $this->registro->getProvaSuperampliada();
        $recursos[] = $this->registro->getAudioDeficienteVisual();
        $recursos[] = $this->registro->getProvaLinguaPortuguesaSegundaLingua();
        $recursos[] = $this->registro->getProvaVideoLibras();
        $recursos[] = $this->registro->getProvaBraille();
        $recursos[] = $this->registro->getNenhumRecurso();
        $recursos = array_diff($recursos, array(null));
        $recursos = array_count_values($recursos);

        if ($recursos < 1 &&
            isset($deficiencias[1]) &&
            $deficiencias[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4))
        ) {
            $this->log(
                'Os Recursos necessários para uso do(a) aluno(a) e para a participação em avaliações do Inep '.
                '(Saeb) não foram informados quando deveriam ser informados.'
            );
        }

        if (($surdocegueira == 1 || $cegueira == 1) &&
            ($auxilioTranscricao == 1 && isset($recursos[1]) && $recursos[1] < 2)) {
            $this->log(
                'Para aluno(a)s com cegueira ou surdocegueira não pode ser informado apenas auxílio transcrição '.
                'como recurso necessário para uso do(a) aluno(a) e para a participação em avaliações do Inep (Saeb).'
            );
        }
    }

    private function validarAuxilioLedor()
    {
        $dado = $this->registro->getAuxilioLedor();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'Auxílio ledor';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $visaoBaixa = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $deficienciaFisica = $this->registro->getDeficienciaFisica();
        $campos[] = $deficienciaIntelectual = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $transtornoAutista = $this->registro->getTranstornoAutista();

        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array(
                $cegueira,
                $visaoBaixa,
                $surdocegueira,
                $deficienciaFisica,
                $deficienciaIntelectual,
                $transtornoAutista
            ))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }
    }

    private function validarAuxilioTranscricao()
    {
        $dado = $this->registro->getAuxilioTranscricao();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'Auxílio transcrição';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $visaoBaixa = $this->registro->getBaixaVisao();
        $campos[] = $visaoMonocular = $this->registro->getVisaoMonocular();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $deficienciaFisica = $this->registro->getDeficienciaFisica();
        $campos[] = $deficienciaIntelectual = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $transtornoAutista = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array(
                $cegueira,
                $visaoBaixa,
                $visaoMonocular,
                $surdocegueira,
                $deficienciaFisica,
                $deficienciaIntelectual,
                $transtornoAutista
            ))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }
    }

    private function validarGuiaInterprete()
    {
        $dado = $this->registro->getGuiaInterprete();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'Guia-Intérprete';

        $campos = array();
        $campos[] = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && $surdocegueira != 1) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }
    }

    private function validarTradutorInterpreteLibras()
    {
        $dado = $this->registro->getTradutorInterpreteLibras();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'Tradutor-Intérprete de Libras';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $surdez = $this->registro->getSurdez();
        $campos[] = $deficienciaAuditiva = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array($surdez, $surdocegueira, $deficienciaAuditiva))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }

        if ($dado == 1 && $cegueira == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Cegueira" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarLeituraLabial()
    {
        $dado = $this->registro->getLeituraLabial();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'Leitura Labial';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $surdez = $this->registro->getSurdez();
        $campos[] = $deficienciaAuditiva = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(16, 7, 18, 11, 41, 27, 28, 32, 33, 37, 38)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array($surdez, $surdocegueira, $deficienciaAuditiva))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }

        if ($dado == 1 && $cegueira == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Cegueira" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarProvaAmpliada()
    {
        $dado = $this->registro->getProvaAmpliada();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $provaSuperampliada = $this->registro->getProvaSuperampliada();
        $provaBraille = $this->registro->getProvaBraille();
        $campo = 'Prova Ampliada (Fonte 18)';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $visaoBaixa = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array($visaoBaixa, $surdocegueira))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }

        if ($dado == 1 && $cegueira == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Cegueira" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && $provaSuperampliada == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Prova superampliada (Fonte 24)"'.
                ' for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && $provaBraille == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Prova superampliada '.
                '(Fonte 24)" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarProvaSuperampliada()
    {
        $dado = $this->registro->getProvaSuperampliada();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $provaBraille = $this->registro->getProvaBraille();
        $campo = 'Prova superampliada (Fonte 24)';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $visaoBaixa = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array($visaoBaixa, $surdocegueira))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }

        if ($dado == 1 && $cegueira == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Cegueira" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && $provaBraille == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Prova superampliada (Fonte 24)"'.
                ' for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarAudioDeficienteVisual()
    {
        $dado = $this->registro->getAudioDeficienteVisual();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'CD com áudio para deficiente visual';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $visaoBaixa = $this->registro->getBaixaVisao();
        $campos[] = $surdez = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $deficienciaFisica = $this->registro->getDeficienciaFisica();
        $campos[] = $deficienciaIntelectual = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $transtornoAutista = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array(
                $cegueira,
                $visaoBaixa,
                $surdocegueira,
                $deficienciaFisica,
                $deficienciaIntelectual,
                $transtornoAutista
            ))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }

        if ($dado == 1 && $surdez == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Surdez" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarProvaLinguaPortuguesaSegundaLingua()
    {
        $dado = $this->registro->getProvaLinguaPortuguesaSegundaLingua();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'Prova de Língua Portuguesa como Segunda Língua para surdos e deficientes auditivos';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $surdez = $this->registro->getSurdez();
        $campos[] = $deficienciaAuditiva = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array($surdez, $deficienciaAuditiva, $surdocegueira))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }

        if ($dado == 1 && $cegueira == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Cegueira" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarProvaVideoLibras()
    {
        $dado = $this->registro->getProvaVideoLibras();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'Prova em Vídeo em Libras';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $surdez = $this->registro->getSurdez();
        $campos[] = $deficienciaAuditiva = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array($deficienciaAuditiva, $surdocegueira, $surdez))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }

        if ($dado == 1 && $cegueira == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Cegueira" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarProvaBraille()
    {
        $dado = $this->registro->getProvaBraille();
        $recursoNenhum = $this->registro->getNenhumRecurso();
        $campo = 'Material didático e prova em Braille';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($recursoNenhum == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }

        if ($dado == 1 && !in_array(1, array($cegueira, $surdocegueira))) {
            $this->log(sprintf('Combinação de tipos de deficiências incompatíveis com o recurso "%s".', $campo));
        }
    }

    private function validarNenhumRecurso()
    {
        $dado = $this->registro->getNenhumRecurso();
        $campo = 'Recursos para uso do(a) aluno(a) em sala de aula e para participação em avaliações do Inep (Saeb)';

        $campos = array();
        $campos[] = $cegueira = $this->registro->getCegueira();
        $campos[] = $this->registro->getBaixaVisao();
        $campos[] = $this->registro->getSurdez();
        $campos[] = $this->registro->getDeficienciaAuditiva();
        $campos[] = $surdocegueira = $this->registro->getSurdocegueira();
        $campos[] = $this->registro->getDeficienciaFisica();
        $campos[] = $this->registro->getDeficienciaintelectual();
        $campos[] = $this->registro->getDeficienciaMultipla();
        $campos[] = $this->registro->getTranstornoAutista();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) &&
            $campos[1] > 0 &&
            !empty($this->registro60) &&
            $this->turmaEtapaAny(array(15, 18, 41, 27, 28, 32, 33)) &&
            $this->turmaModalidadeAny(array(1, 4)) &&
            !$this->isPreenchido($dado)
        ) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && isset($campos[1]) && $campos[1] <= 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($cegueira == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Cegueira" for preenchido com a "Sim".',
                $campo
            ));
        }

        if ($surdocegueira == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Surdocegueira" for preenchido '.
                'com a "Sim".',
                $campo
            ));
        }
    }

    private function validarNis()
    {
        $dado = $this->registro->getNis();
        $campo = 'Número de Identificação Social (NIS)';

//        if ($this->isPreenchido($dado) && empty($this->registro60)) {
//            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
//        }
//
//        if (!$this->isPreenchido($dado)) {
//            return;
//        }
//
//        if (strlen($dado) != 11) {
//            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
//        }
//
//        if (!DBString::isSomenteNumero($dado)) {
//            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
//        }
//
//        if ($dado === "00000000000") {
//            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
//        }
    }

    private function validarCertidaoNascimento()
    {
        $dado = $this->registro->getCertidaoNascimento();
        $campo = 'Número da matrícula da certidão de nascimento (certidão nova)';

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) != 32) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }
        $tipoCertidao = intval(substr($dado, 14, 1));

        if ($tipoCertidao === 2) {
            $this->log("A certidão informada não pode ser de casamento.");
        }
        $ano = intval(substr($dado, 10, 4));
        $nascimento = \DateTime::createFromFormat('d/m/Y', $this->registro->getDataNascimento());
        $nascimento = $nascimento->format('Y');
        if (!verificaValidadeNumeroMatriculaCertidao($dado) ||
            $ano < $nascimento ||
            $ano > (new \DateTime())->format('Y')

        ) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        // TODO: Validar regras 4 - 8 do campo 40
    }

    private function validarJustificativaFaltaDocumentacao()
    {
        $dado = $this->registro->getJustificativaFaltaDocumentacao();
        $cpf = $this->registro->getCpf();
        $nis = $this->registro->getNis();
        $certidaoNasc = $this->registro->getCertidaoNascimento();
        $campo = 'Justificativa da falta de documentação';

        if (!$this->isPreenchido($cpf) &&
            !$this->isPreenchido($nis) &&
            !$this->isPreenchido($certidaoNasc) &&
            !$this->isPreenchido($dado) &&
            !empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $this->isPreenchido($cpf)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $this->isPreenchido($nis)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $this->isPreenchido($certidaoNasc)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarPaisResidencia()
    {
        $dado = $this->registro->getPaisResidencia();
        $campo = 'País de residência';

        if ($this->isPreenchido($dado) && empty($this->registros50) && empty($this->registro60)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(32, 68, 76, 170, 328, 254, 600, 604, 740, 858, 862))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCep()
    {
        $dado = $this->registro->getCep();
        $paisResidencia = $this->registro->getPaisResidencia();
        $campo = 'CEP';

        if ($this->isPreenchido($dado) && $paisResidencia != 76) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }
    }

    private function validarMunicipioResidencia()
    {
        $dado = $this->registro->getMunicipioResidencia();

        $cep = $this->registro->getCep();

        $campo = 'Município de residência';

        if (!$this->isPreenchido($dado) && $this->isPreenchido($cep)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && !$this->isPreenchido($cep)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }
    }

    private function validarZonaResidencia()
    {
        $dado = $this->registro->getZonaResidencia();
        $paisResidencia = $this->registro->getPaisResidencia();
        $campo = 'Localização/ Zona de residência';

        if (!$this->isPreenchido($dado) && $paisResidencia == 76) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $paisResidencia != 76) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarLocalizacaoDiferenciada()
    {
        $dado = $this->registro->getLocalizacaoDiferenciada();
        $paisResidencia = $this->registro->getPaisResidencia();
        $zona = $this->registro->getZonaResidencia();
        $campo = 'Localização diferenciada de residência';

        if ($this->isPreenchido($dado) && $paisResidencia != 76) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2, 3, 7, 8))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $zona == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Área de assentamento" quando o campo '.
                '"Localização/ Zona de residência" for preenchido com "Urbana".',
                $campo
            ));
        }
    }

    private function validarEscolaridade()
    {
        $dado = $this->registro->getEscolaridade();
        $campo = 'Maior nível de escolaridade concluído';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2, 7, 6))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarTipoEnsinoMedio()
    {
        $dado = $this->registro->getTipoEnsinoMedio();
        $escolaridade = $this->registro->getEscolaridade();
        $campo = 'Tipo de ensino médio cursado';

        if ($escolaridade == 7 && !$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($escolaridade, array(7, 6)) && $this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2, 3, 4))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCodigoCurso1()
    {
        $dado = $this->registro->getCodigoCurso1();
        $escolaridade = $this->registro->getEscolaridade();
        $campo = 'Código do Curso 1';

        if (!$this->isPreenchido($dado) && $escolaridade == 6) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $escolaridade != 6) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado)) {
            $curso = $this->registro->getNomeCurso1();

            if (!$this->registro->isAtivo1()) {
                $this->log(sprintf('O curso "%s" não esta mais ativo.', $curso));
                return;
            }
        }
    }

    private function validarAnoConclusao1()
    {
        $dado = $this->registro->getAnoConclusao1();
        $codigoCurso = $this->registro->getCodigoCurso1();
        $dataNascimento = $this->registro->getDataNascimento();
        $anoAtual = DBDate::now()->getDate('Y');
        $campo = 'Ano de Conclusão 1';

        if (!$this->isPreenchido($dado) && $this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && !$this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) != 4) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado < 1940) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado > $anoAtual) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($this->isPreenchido($dataNascimento)) {
            $anoNasc = DBDate::create($dataNascimento)->getDate('Y');
            if ($dado <= $anoNasc) {
                $this->log(sprintf(
                    'O campo "%s" não pode ser anterior ou igual ao ano informado no campo "Data de nascimento".',
                    $campo
                ));
            }
        }
    }

    private function validarInstituicaoSuperior1()
    {
        $dado = $this->registro->getInstituicaoSuperior1();
        $codigoCurso = $this->registro->getCodigoCurso1();
        $campo = 'Instituição de educação superior 1';

        if (!$this->isPreenchido($dado) && $this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && !$this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }
    }

    private function validarCodigoCurso2()
    {
        $dado = $this->registro->getCodigoCurso2();
        $codigoCurso1 = $this->registro->getCodigoCurso1();
        $campo = 'Código do Curso 2';

        if ($this->isPreenchido($dado) && !$this->isPreenchido($codigoCurso1)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if ($dado == $codigoCurso1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com o mesmo valor do campo "Código do Curso 1".',
                $campo
            ));
        }

        if ($this->isPreenchido($dado)) {
            $curso = $this->registro->getNomeCurso2();

            if (!$this->registro->isAtivo2()) {
                $this->log(sprintf('O curso "%s" não esta mais ativo.', $curso));
                return;
            }
        }
    }

    private function validarAnoConclusao2()
    {
        $dado = $this->registro->getAnoConclusao2();
        $codigoCurso = $this->registro->getCodigoCurso2();
        $dataNascimento = $this->registro->getDataNascimento();
        $anoAtual = DBDate::now()->getDate('Y');
        $campo = 'Ano de Conclusão 2';

        if (!$this->isPreenchido($dado) && $this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && !$this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) != 4) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado < 1940) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado > $anoAtual) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $anoNasc = DBDate::create($dataNascimento)->getDate('Y');
        if ($dado <= $anoNasc) {
            $this->log(sprintf(
                'O campo "%s" não pode ser anterior ou igual ao ano informado no campo "Data de nascimento".',
                $campo
            ));
        }
    }

    private function validarInstituicaoSuperior2()
    {
        $dado = $this->registro->getInstituicaoSuperior2();
        $codigoCurso = $this->registro->getCodigoCurso2();
        $campo = 'Instituição de educação superior 2';

        if (!$this->isPreenchido($dado) && $this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && !$this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }
    }

    private function validarCodigoCurso3()
    {
        $dado = $this->registro->getCodigoCurso3();
        $codigoCurso1 = $this->registro->getCodigoCurso1();
        $codigoCurso2 = $this->registro->getCodigoCurso2();
        $campo = 'Código do Curso 2';

        if ($this->isPreenchido($dado) && !$this->isPreenchido($codigoCurso2)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if ($dado == $codigoCurso1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com o mesmo valor do campo "Código do Curso 1".',
                $campo
            ));
        }

        if ($dado == $codigoCurso2) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com o mesmo valor do campo "Código do Curso 2".',
                $campo
            ));
        }

        if ($this->isPreenchido($dado)) {
            $curso = $this->registro->getNomeCurso3();

            if (!$this->registro->isAtivo3()) {
                $this->log(sprintf('O curso "%s" não esta mais ativo.', $curso));
                return;
            }
        }
    }

    private function validarAnoConclusao3()
    {
        $dado = $this->registro->getAnoConclusao3();
        $codigoCurso = $this->registro->getCodigoCurso3();
        $dataNascimento = $this->registro->getDataNascimento();
        $anoAtual = DBDate::now()->getDate('Y');
        $campo = 'Ano de Conclusão 3';

        if (!$this->isPreenchido($dado) && $this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && !$this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) != 4) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado < 1940) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado > $anoAtual) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $anoNasc = DBDate::create($dataNascimento)->getDate('Y');
        if ($dado <= $anoNasc) {
            $this->log(sprintf(
                'O campo "%s" não pode ser anterior ou igual ao ano informado no campo "Data de nascimento".',
                $campo
            ));
        }
    }

    private function validarInstituicaoSuperior3()
    {
        $dado = $this->registro->getInstituicaoSuperior3();
        $codigoCurso = $this->registro->getCodigoCurso3();
        $campo = 'Instituição de educação superior 3';

        if (!$this->isPreenchido($dado) && $this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && !$this->isPreenchido($codigoCurso)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }
    }

    private function validarComponenteCurricular1()
    {
        $dado = $this->registro->getComponenteCurricular1();
        $escolaridade = $this->registro->getEscolaridade();
        $campo = 'Área do conhecimento/componentes curriculares 1';

        if ($this->isPreenchido($dado) && $escolaridade != 6 && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($dado, array(32, 99))) {
            $this->log(sprintf('O campo "%s foi preenchido quando não deveria ser preenchido.', $campo));
        }
    }

    private function validarComponenteCurricular2()
    {
        $dado = $this->registro->getComponenteCurricular2();
        $componente1 = $this->registro->getComponenteCurricular1();
        $campo = 'Área do conhecimento/componentes curriculares 2';

        if ($this->isPreenchido($dado) && !$this->isPreenchido($componente1)) {
            $this->log(sprintf('O campo "%s foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (in_array($dado, array(32, 99))) {
            $this->log(sprintf('O campo "%s foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dado == $componente1) {
            $this->log(sprintf(
                'O campo "%s não pode ser preenchido com o mesmo valor do campo "Área do '.
                'conhecimento/componentes curriculares 1".',
                $campo
            ));
        }
    }

    private function validarComponenteCurricular3()
    {
        $dado = $this->registro->getComponenteCurricular3();
        $componente1 = $this->registro->getComponenteCurricular1();
        $componente2 = $this->registro->getComponenteCurricular2();
        $campo = 'Área do conhecimento/componentes curriculares 3';

        if ($this->isPreenchido($dado) && !$this->isPreenchido($componente2)) {
            $this->log(sprintf('O campo "%s foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (in_array($dado, array(32, 99))) {
            $this->log(sprintf('O campo "%s foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dado == $componente1) {
            $this->log(sprintf(
                'O campo "%s não pode ser preenchido com o mesmo valor do campo "Área do conhecimento/componentes'.
                ' curriculares 1".',
                $campo
            ));
        }

        if ($dado == $componente2) {
            $this->log(sprintf(
                'O campo "%s não pode ser preenchido com o mesmo valor do campo "Área do conhecimento/componentes'.
                ' curriculares 2".',
                $campo
            ));
        }
    }

    private function validarPosGraduacoes()
    {
        $posGraduacoes = $this->registro->getPosGraduacoes();
        $escolaridade = $this->registro->getEscolaridade();
        $nenhumaPos = empty($this->registro->getPosGraduacoes());
        $verificaPos = [];
        if ($nenhumaPos || $escolaridade != 6) {
            return;
        } else {
            foreach ($posGraduacoes as $key => $pos) {
                $hash = "{$pos['ed183_tipoformacao']}#{$pos['ed183_areaformacao']}#{$pos['ed183_anoconclusao']}";
                $verificaPos[$hash][] = $pos;
                $this->validarTipoPosGraduacao($pos, $key);
                $this->validarAreaPosGraduacao($pos, $key);
                $this->validarAnoConclusaoPosGraduacao($pos, $key);
            }

            foreach ($verificaPos as $hash => $pos) {
                if (count($pos) > 1) {
                    $msg = "Pós-Graduações concluídas não foram preenchidas corretamente. Não pode haver duas ou";
                    $msg .= " mais pós-graduações concluídas com todos os seus campos (Tipo de pós-graduação, ";
                    $msg .= "Área da pós-graduação e Ano de conclusão da pós-graduação) iguais.";
                    $this->log($msg);
                }
            }
        }
    }

    private function validarTipoPosGraduacao($pos, $key)
    {
        $campo = sprintf('Tipo de pós-graduação %s', $key + 1);
        $dado = $pos['ed183_tipoformacao'];
        if (!in_array($dado, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAreaPosGraduacao($pos, $key)
    {
        $areas = $this->getAreasPos();
        $campo = sprintf('Área da pós-graduação %s', $key + 1);
        $dado = $pos['ed183_areaformacao'];

        if (!in_array($dado, $areas)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAnoConclusaoPosGraduacao($pos, $key)
    {
        $campo = sprintf('Ano de conclusão da pós-graduação ', $key + 1);
        $anosConclusoesMedio = [];

        if (!is_null($this->registro->getAnoConclusao1())) {
            $anosConclusoesMedio[] = $this->registro->getAnoConclusao1();
        }
        if (!is_null($this->registro->getAnoConclusao2())) {
            $anosConclusoesMedio[] = $this->registro->getAnoConclusao2();
        }
        if (!is_null($this->registro->getAnoConclusao3())) {
            $anosConclusoesMedio[] = $this->registro->getAnoConclusao3();
        }

        $dado = $pos['ed183_anoconclusao'];
        $anoInferior = 0;
        foreach ($anosConclusoesMedio as $ano) {
            if ($dado < $ano) {
                $anoInferior++;
            }
        }
        if (count($anosConclusoesMedio) == $anoInferior) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEspecializacao()
    {
        $dado = $this->registro->getEspecializacao();
        $escolaridade = $this->registro->getEscolaridade();
        $nenhumaPos = $this->registro->getNenhumaPos();
        $campo = 'Pós-Graduações concluídas - Especialização';

        if (!$this->isPreenchido($dado) && $escolaridade == 6) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $escolaridade != 6) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $nenhumaPos == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não tem pós-graduação concluída" '.
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarMestrado()
    {
        $dado = $this->registro->getMestrado();
        $escolaridade = $this->registro->getEscolaridade();
        $nenhumaPos = $this->registro->getNenhumaPos();
        $campo = 'Pós-Graduações concluídas - Mestrado';

        if (!$this->isPreenchido($dado) && $escolaridade == 6) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $escolaridade != 6) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $nenhumaPos == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não tem pós-graduação concluída" '.
                'for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarDoutorado()
    {
        $dado = $this->registro->getDoutorado();
        $escolaridade = $this->registro->getEscolaridade();
        $nenhumaPos = $this->registro->getNenhumaPos();
        $campo = 'Pós-Graduações concluídas - Doutorado';

        if (!$this->isPreenchido($dado) && $escolaridade == 6) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $escolaridade != 6) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $nenhumaPos == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Não tem pós-graduação concluída"'.
                ' for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarNenhumaPos()
    {
        $nenhumaPos = $this->registro->getNenhumaPos();
        $escolaridade = $this->registro->getEscolaridade();
        $campo = 'Não tem pós-graduação concluída';

        if ($nenhumaPos == 1 && $escolaridade != 6) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($nenhumaPos != 1 && $nenhumaPos != null) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarOutrosCursos()
    {
        $campos = array();
        $campos[] = $this->registro->getCreche();
        $campos[] = $this->registro->getPreEscola();
        $campos[] = $this->registro->getAnosIniciais();
        $campos[] = $this->registro->getAnosFinais();
        $campos[] = $this->registro->getEnsinoMedio();
        $campos[] = $this->registro->getEja();
        $campos[] = $this->registro->getEducacaoEspecial();
        $campos[] = $this->registro->getEducacaoIndigena();
        $campos[] = $this->registro->getEducacaoCampo();
        $campos[] = $this->registro->getEducacaoAmbiental();
        $campos[] = $this->registro->getEducacaoDireitosHumanos();
        $campos[] = $this->registro->getGeneroDiversidadeSexual();
        $campos[] = $this->registro->getDireitosCriancaAdolescente();
        $campos[] = $this->registro->getEducacaoEtnicoRaciais();
        $campos[] = $this->registro->getGestaoEscolar();
        $campos[] = $this->registro->getOutros();
        $campos[] = $this->registro->getNenhumCurso();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if (isset($campos[1]) && $campos[1] < 1) {
            $this->log(
                '"Outros cursos específicos (Formação continuada com mínimo de 80 horas)" não foram preenchidos '.
                'corretamente. Não podem ser informadas todas as opções com valor igual a "Não".'
            );
        }
    }

    private function validarCreche()
    {
        $dado = $this->registro->getCreche();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Creche (0 a 3 anos)';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarPreEscola()
    {
        $dado = $this->registro->getPreEscola();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Pré-escola (4 e 5 anos)';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarAnosIniciais()
    {
        $dado = $this->registro->getAnosIniciais();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Anos iniciais do ensino ';
        $campo .= 'fundamental';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarAnosFinais()
    {
        $dado = $this->registro->getAnosFinais();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Anos finais do ensino ';
        $campo .= 'fundamental';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEnsinoMedio()
    {
        $dado = $this->registro->getEnsinoMedio();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Ensino médio';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEja()
    {
        $dado = $this->registro->getEja();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Educação de jovens e ';
        $campo .= 'adultos';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEducacaoEspecial()
    {
        $dado = $this->registro->getEducacaoEspecial();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Educação especial';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEducacaoIndigena()
    {
        $dado = $this->registro->getEducacaoIndigena();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Educação Indígena';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEducacaoCampo()
    {
        $dado = $this->registro->getEducacaoCampo();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Educação do campo';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEducacaoAmbiental()
    {
        $dado = $this->registro->getEducacaoAmbiental();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Educação ambiental';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEducacaoTIC()
    {
        $dado = $this->registro->getEducacaoTIC();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Educação e Tecnologia de Informação e Comunicação (TIC)';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEducacaoBilingueSurdos()
    {
        $dado = $this->registro->getEducacaoBilingueSurdos();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Educação bilíngue de surdos';
        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEducacaoDireitosHumanos()
    {
        $dado = $this->registro->getEducacaoDireitosHumanos();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Educação em direitos';
        $campo .= ' humanos';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarGeneroDiversidadeSexual()
    {
        $dado = $this->registro->getGeneroDiversidadeSexual();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Gênero e diversidade sexual';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarDireitosCriancaAdolescente()
    {
        $dado = $this->registro->getDireitosCriancaAdolescente();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Direitos de criança e';
        $campo .= ' adolescente';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarEducacaoEtnicoRaciais()
    {
        $dado = $this->registro->getEducacaoEtnicoRaciais();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Educação para as relações';
        $campo .= ' étnico-raciais e História e cultura Afro-Brasileira e Africana';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarGestaoEscolar()
    {
        $dado = $this->registro->getGestaoEscolar();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Gestão Escolar';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarOutros()
    {
        $dado = $this->registro->getOutros();
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Outros';

        if (!$this->isPreenchido($dado) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($nenhumCurso == 1 && $dado == 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Sim" quando o campo "Nenhum" for preenchido com "Sim".',
                $campo
            ));
        }
    }

    private function validarNenhumCurso()
    {
        $nenhumCurso = $this->registro->getNenhumCurso();
        $campo = 'Outros cursos específicos (Formação continuada com mínimo de 80 horas) - Nenhum';

        if (!$this->isPreenchido($nenhumCurso) && (!empty($this->registro40) || !empty($this->registros50))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($nenhumCurso) && empty($this->registro40) && empty($this->registros50)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($nenhumCurso)) {
            return;
        }

        if (!in_array($nenhumCurso, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEmail()
    {
        $dado = $this->registro->getEmail();
        $campo = 'Endereço Eletrônico (e-mail)';

        if (!$this->isPreenchido($dado) && !empty($this->registro40)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && empty($this->registro40)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) > 100) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        $regex = "/^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)";
        $regex .= "*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|";
        $regex .= "([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i";
        if (!preg_match($regex, $dado)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function getAreasPos()
    {
        $areas = [];
        $sql = "select ed184_id from censoareaspos";
        $rs = db_query($sql);
        while ($area = pg_fetch_assoc($rs)) {
            $areas[] = $area['ed184_id'];
        }
        return $areas;
    }
}
