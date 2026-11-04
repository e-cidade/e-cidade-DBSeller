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

use ECidade\Educacao\Escola\Censo\Log\LogMatriculaInicial;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro00;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro10;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro20;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro50;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro60;
use ECidade\Educacao\Escola\Censo\Registry\LogCensoRegistry;
use Exception;
use ECidade\Educacao\Escola\Censo\Helpers\Turma;

class Registro20Validator
{
    /**
     * @var Registro20
     */
    private $registro;

    /**
     * @var Registro00
     */
    private $registro00;

    /**
     * @var Registro10
     */
    private $registro10;

    /**
     * @var Registro50[]
     */
    private $registros50 = array();

    /**
     * @var Registro60[]
     */
    private $registros60 = array();

    public function setRegistro(Registro20 $registro)
    {
        $this->registro = $registro;
    }

    public function setRegistro00(Registro00 $registro00)
    {
        $this->registro00 = $registro00;
    }

    public function setRegistro10(Registro10 $registro10)
    {
        $this->registro10 = $registro10;
    }

    /**
     * @param $dado
     * @throws Exception
     */
    public function log($dado)
    {
        $codigo = Turma::decodeCodigoTurma($this->registro->getCodigoTurma());

        $dado = "Turma {$codigo} - {$this->registro->getNomeTurma()}: $dado";

        $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
        $log->add(LogMatriculaInicial::REGISTRO20, $dado);
    }

    /**
     * Diz se um campo foi preenchido
     * @return boolean
     */
    private function isPreenchido($campo)
    {
        return !is_null($campo) && $campo !== "";
    }

    public function validar()
    {
        $this->validarRegrasGerais();
        $this->validarTipoRegistro();
        $this->validarCodigoInepEscola();
        $this->validarCodigoTurma();
        $this->validarCodigoInep();
        $this->validarNomeTurma();
        $this->validarTipoMediacaoDidaticoPedagogica();
        $this->validarHorarioFuncionamento();
        $this->validarHoraInicio();
        $this->validarMinutoInicio();
        $this->validarHoraFim();
        $this->validarMinutoFim();
        $this->validarDiasSemana();
        $this->validarDomingo();
        $this->validarSegundaFeira();
        $this->validarTercaFeira();
        $this->validarQuartaFeira();
        $this->validarQuintaFeira();
        $this->validarSextaFeira();
        $this->validarSabado();
        $this->validarTiposAtendimento();
        $this->validarEscolarizacao();
        $this->validarAtividadeComplementar();
        $this->validarAtendimentoAEE();
        $this->validarTipoAtividadeComplementar();
        $this->validarCodigo1();
        $this->validarCodigo2();
        $this->validarCodigo3();
        $this->validarCodigo4();
        $this->validarCodigo5();
        $this->validarCodigo6();
        $this->validarLocalFuncionamentoDiferenciado();
        $this->validarModalidade();
        $this->validarEtapa();
        $this->validarCodigoCurso();
        $this->validarQuimica();
        $this->validarFisica();
        $this->validarMatematica();
        $this->validarBiologia();
        $this->validarCiencias();
        $this->validarLiteraturaPortuguesa();
        $this->validarLiteraturaEstrangeiraIngles();
        $this->validarLiteraturaEstrangeiraEspanhol();
        $this->validarLiteraturaEstrangeiraOutra();
        $this->validarArtes();
        $this->validarEducacaoFisica();
        $this->validarHistoria();
        $this->validarGeografia();
        $this->validarFilosofia();
        $this->validarInformatica();
        $this->validarCursosTecnicosProfissionais();
        $this->validarLibras();
        $this->validarDisciplinasPedagogicas();
        $this->validarEnsinoReligioso();
        $this->validarLinguaIndigena();
        $this->validarEstudosSociais();
        $this->validarSociologia();
        $this->validarLiteraturaEstrangeiraFrances();
        $this->validarPortuguesComoSegundaLingua();
        $this->validarEstagioSupervisionado();
        $this->validarOutrasDisciplinas();
        $this->validarEstruturaCurricular();
        $this->validarEletivas();
        $this->validarLibrasUnidadeCurricular();
        $this->validarLinguaIndigenaUnidade();
        $this->validarLinguaEspanhol();
        $this->validarLinguaFrances();
        $this->validarLinguaOutra();
        $this->validarProjetoVidaUnidade();
        $this->validarTrilhaAprofundamento();
        $this->validarOutrasUnidades();
        //$this->validarFormaOrganizacaoTurma();
    }

    private function validarOutrasUnidades()
    {
        $campo = 'Outra(s) unidade(s) curricular(es) obrigatória(s)';
        $dado = $this->registro->getOutrasUnidades();
        if ($this->isPreenchido($dado)) {
            if (strlen($dado) < 4 || strlen($dado > 100)) {
                $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
            }
        }
    }
    private function validarTrilhaAprofundamento()
    {
        $campo = 'Trilhas de aprofundamento/aprendizagens';
        $dado = $this->registro->getTrilhaAprofundamento();

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }
    private function validarProjetoVidaUnidade()
    {
        $campo = 'Projeto de vida';
        $dado = $this->registro->getProjetoVidaUnidade();

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarLinguaOutra()
    {
        $campo = 'Língua/Literatura estrangeira - outra';
        $dado = $this->registro->getLinguaOutra();

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarLinguaFrances()
    {
        $campo = 'Língua/Literatura estrangeira - Francês';
        $dado = $this->registro->getLinguaFrances();

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }
    private function validarLinguaEspanhol()
    {
        $campo = 'Língua/Literatura estrangeira - Espanhol';
        $dado = $this->registro->getLinguaEspanhol();

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }
    private function validarLinguaIndigenaUnidade()
    {
        $campo = 'Língua indígena';
        $dado = $this->registro->getLinguaIndigenaUnidade();

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }
    private function validarLibrasUnidadeCurricular()
    {
        $campo = 'Libras';
        $dado = $this->registro->getLibrasUnidadeCurricular();

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }
    private function validarEletivas()
    {
        $campo = 'Eletivas';
        $dado = $this->registro->getEletivas();

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarFormaOrganizacaoTurma()
    {
        if (is_null($this->registro->getFromaOrganizaTurma()[0])) {
            $this->log("precisa cadastrar forma de oprganizacap da turma
            no cadastro de regime de matricula");
        }
    }

    private function validarEstruturaCurricular()
    {

        if (($this->registro->getAtendimentoAEE() == 1 || $this->registro->getAtividadeComplementar() == 1) &&
                $this->registro->getEscolarizacao() == 0) {
            return;
        }
        if (!$this->validarFormacaoGeralBasica() &&
            !$this->validarItinerarioFormativo() &&
            !$this->validarNaoAplica()
        ) {
            $log = '"Estrutura curricular" não foi preenchido corretamente. Não podem ser informadas todas as ';
            $log .= 'opções com valor igual a 0 (Não).';
            $this->log($log);
        }
    }

    private function validarFormacaoGeralBasica()
    {
        $campo = 'Formação geral básica';
        $dado = $this->registro->getFormacaoGeralBasica();
        $naoAplica = $this->registro->getNaoAplica();
        $escolarizacao = $this->registro->getEscolarizacao();
        $tipoMediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();

        if ($escolarizacao == 1 && is_null($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($escolarizacao != 1 && $dado == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $naoAplica == 1) {
            $log = 'O campo "%s" não pode ser preenchido com 1 (Sim) quando o campo "Não se aplica" ';
            $log .= 'for preenchido com 1 (Sim).';
            $this->log(sprintf($log, $campo));
        }

        if ($dado == 0 && $tipoMediacao == 2) {
            $log = 'O campo "%s" não pode ser preenchido com "Não" quando o campo ';
            $log .= '"Tipo de mediação didático pedagógica" for preenchido com "Semipresencial".';
            $this->log(sprintf($log, $campo));
        }

        if ($dado == 0) {
            return false;
        }

        return true;
    }

    private function validarItinerarioFormativo()
    {
        $campo = 'Itinerário Formativo';
        $dado = $this->registro->getItinerarioFormativo();
        $naoAplica = $this->registro->getNaoAplica();
        $escolarizacao = $this->registro->getEscolarizacao();

        if ($escolarizacao == 1 && is_null($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($escolarizacao != 1 && $dado == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && $naoAplica == 1) {
            $log = 'O campo "%s" não pode ser preenchido com 1 (Sim) quando o campo "Não se aplica" ';
            $log .= 'for preenchido com 1 (Sim).';
            $this->log(sprintf($log, $campo));
        }

        if ($dado == 0) {
            return false;
        }

        return true;
    }

    private function validarNaoAplica()
    {
        $campo = 'Itinerário Formativo';
        $dado = $this->registro->getNaoAplica();
        $escolarizacao = $this->registro->getEscolarizacao();

        if ($escolarizacao == 1 && is_null($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($escolarizacao != 1 && $dado == 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($dado, [0, 1])) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 0) {
            return false;
        }

        return true;
    }

    private function validarTipoRegistro()
    {
        if ($this->registro->getTipoRegistro() !== '20') {
            $this->log('O tipo de registro deve ser "20"');
        }
    }

    private function validarCodigoInepEscola()
    {
        $inep = $this->registro->getCodigoInepEscola();
        $inepRegistro00 = $this->registro00->getCodigoInep();
        $campo = 'Código de escola - Inep';

        if (!$this->isPreenchido($inep)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($inep != $inepRegistro00) {
            $this->log(sprintf('O campo "%s" está diferente do registro 00 antecedente.', $campo));
        }
    }

    private function validarCodigoTurma()
    {
        $codigo = $this->registro->getCodigoTurma();
        $campo = 'Código da Turma na Entidade/Escola';

        if (!$this->isPreenchido($codigo)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (strlen($codigo) > 20) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        // Validacao da regra 3 do campo 3 (turma informada duas vezes na mesma escola)
        // garantida pela verificacao de integridade do sistema
    }

    private function validarCodigoInep()
    {
        $codigo = $this->registro->getCodigoInep();
        $campo = 'Código da Turma - Inep';

        if ($this->isPreenchido($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando deveria não ser preenchido.', $campo));
        }
    }

    private function validarNomeTurma()
    {
        $nome = $this->registro->getNomeTurma();
        $campo = 'Nome da Turma';
        if (!$this->isPreenchido($nome)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarTipoMediacaoDidaticoPedagogica()
    {
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Tipo de mediação didático-pedagógica';

        if (!$this->isPreenchido($mediacao)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($mediacao, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarHorarioFuncionamento()
    {
        $horaInicio = $this->registro->getHoraInicio();
        $minutoInicio = $this->registro->getMinutoInicio();
        $horaFim = $this->registro->getHoraFim();
        $minutoFim = $this->registro->getMinutoFim();
        $escolarizacao = $this->registro->getEscolarizacao();
        $atividadeComplementar = $this->registro->getAtividadeComplementar();

        $inicioMinutosTotal = $horaInicio * 60 + $minutoInicio;
        $fimMinutosTotal = $horaFim * 60 + $minutoFim;

        if ($inicioMinutosTotal >= $fimMinutosTotal) {
            $this->log('A Hora Inicial não pode ser maior ou igual à Hora Final".');
        }

        if ($atividadeComplementar == 1 && $escolarizacao == 1) {
            $totalHoras = ($fimMinutosTotal - $inicioMinutosTotal) / 60;
            if ($totalHoras < 5) {
                $this->log(
                    'A turma de escolarização e atividade complementar foi preenchida com turno menor que 5 horas.'
                );
            }
        }
    }

    private function validarHoraInicio()
    {
        $horaInicio = $this->registro->getHoraInicio();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Horário de funcionamento - Hora Inicial';

        if ($mediacao == 1 && !$this->isPreenchido($horaInicio)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($mediacao != 1 && $this->isPreenchido($horaInicio)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($horaInicio)) {
            return;
        }

        if ($horaInicio < 0 || $horaInicio > 23) {
            $this->log(sprintf('O campo "%s" deve estar entre 00 e 23.', $campo));
        }
    }

    private function validarMinutoInicio()
    {
        $minutoInicio = $this->registro->getMinutoInicio();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Horário de funcionamento - Minuto Inicial';

        if ($mediacao == 1 && !$this->isPreenchido($minutoInicio)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($mediacao != 1 && $this->isPreenchido($minutoInicio)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($minutoInicio)) {
            return;
        }

        if (!($minutoInicio >= 0 && $minutoInicio <= 55 && $minutoInicio % 5 == 0)) {
            $this->log(sprintf('O campo "%s" deve ser múltiplo de 5.', $campo));
        }
    }

    private function validarHoraFim()
    {
        $horaFim = $this->registro->getHoraFim();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Horário de funcionamento - Hora Final';

        if ($mediacao == 1 && !$this->isPreenchido($horaFim)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($mediacao != 1 && $this->isPreenchido($horaFim)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($horaFim)) {
            return;
        }

        if ($horaFim < 0 || $horaFim > 23) {
            $this->log(sprintf('O campo "%s" deve ser múltiplo de 5.', $campo));
        }
    }

    private function validarMinutoFim()
    {
        $minutoFim = $this->registro->getMinutoFim();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Horário de funcionamento - Minuto Final';

        if ($mediacao == 1 && !$this->isPreenchido($minutoFim)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($mediacao != 1 && $this->isPreenchido($minutoFim)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($minutoFim)) {
            return;
        }

        if (!($minutoFim >= 0 && $minutoFim <= 55 && $minutoFim % 5 == 0)) {
            $this->log(sprintf('O campo "%s" deve ser múltiplo de 5.', $campo));
        }
    }

    private function validarDiasSemana()
    {
        $campos = array();
        $campos[] = $this->registro->getDomingo();
        $campos[] = $this->registro->getSegundaFeira();
        $campos[] = $this->registro->getTercaFeira();
        $campos[] = $this->registro->getQuartaFeira();
        $campos[] = $this->registro->getQuintaFeira();
        $campos[] = $this->registro->getSextaFeira();
        $campos[] = $this->registro->getSabado();

        // Remover campos nulos...
        $campos = array_diff($campos, array(null));

        // Se pelo menos um dos campos está preenchido e nenhum deles for 1...
        if (count($campos) > 0 && !in_array(1, $campos)) {
            $mensagem = '"Dias da Semana" não foram preenchidos corretamente. ';
            $mensagem .= 'Não podem ser informadas todas as opções com valor igual a "Não".';
            $this->log($mensagem);
        }
    }

    private function validarDomingo()
    {
        $domingo = $this->registro->getDomingo();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Dias da Semana - Domingo';

        if ($mediacao == 1 && !$this->isPreenchido($domingo)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($mediacao != 1 && $this->isPreenchido($domingo)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($domingo)) {
            return;
        }

        if (!in_array($domingo, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarSegundaFeira()
    {
        $segunda = $this->registro->getSegundaFeira();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Dias da Semana - Segunda-Feira';

        if ($mediacao == 1 && !$this->isPreenchido($segunda)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($mediacao != 1 && $this->isPreenchido($segunda)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($segunda)) {
            return;
        }

        if (!in_array($segunda, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarTercaFeira()
    {
        $terca = $this->registro->getTercaFeira();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Dias da Semana - Terça-feira';

        if ($mediacao == 1 && !$this->isPreenchido($terca)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($mediacao != 1 && $this->isPreenchido($terca)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($terca)) {
            return;
        }

        if (!in_array($terca, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarQuartaFeira()
    {
        $quarta = $this->registro->getQuartaFeira();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Dias da Semana - Quarta-feira';

        if ($mediacao == 1 && !$this->isPreenchido($quarta)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($mediacao != 1 && $this->isPreenchido($quarta)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($quarta)) {
            return;
        }

        if (!in_array($quarta, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarQuintaFeira()
    {
        $quinta = $this->registro->getQuintaFeira();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Dias da Semana - Quinta-feira';

        if ($mediacao == 1 && !$this->isPreenchido($quinta)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($mediacao != 1 && $this->isPreenchido($quinta)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($quinta)) {
            return;
        }

        if (!in_array($quinta, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarSextaFeira()
    {
        $sexta = $this->registro->getSextaFeira();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Dias da Semana - Sexta-feira';

        if ($mediacao == 1 && !$this->isPreenchido($sexta)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($mediacao != 1 && $this->isPreenchido($sexta)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($sexta)) {
            return;
        }

        if (!in_array($sexta, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarSabado()
    {
        $sabado = $this->registro->getSabado();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Dias da Semana - Sábado';

        if ($mediacao == 1 && !$this->isPreenchido($sabado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($mediacao != 1 && $this->isPreenchido($sabado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($sabado)) {
            return;
        }

        if (!in_array($sabado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarTiposAtendimento()
    {
        $campos = array();
        $campos[] = $this->registro->getEscolarizacao();
        $campos[] = $this->registro->getAtividadeComplementar();
        $campos[] = $this->registro->getAtendimentoAEE();

        if (!in_array(1, $campos)) {
            $mensagem = '"Tipo de atendimento" não foi preenchido corretamente. ';
            $mensagem .= 'Não podem ser informadas todas as opções com valor igual a "Não".';
            $this->log($mensagem);
        }
    }

    private function validarEscolarizacao()
    {
        $escolarizacao = $this->registro->getEscolarizacao();
        $aee = $this->registro->getAtendimentoAEE();
        $campo = 'Tipo de atendimento - Escolarização';

        if (!$this->isPreenchido($escolarizacao)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($escolarizacao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($escolarizacao == 1 && $aee == 1) {
            $msgAtendimento = '"Atendimento educacional especializado - AEE"';
            $this->log(
                sprintf(
                    'O campo "%s" não pode ser preenchido com "Sim" quando o campo %s for preenchido com "Sim".',
                    $campo,
                    $msgAtendimento
                )
            );
        }
    }

    private function validarAtividadeComplementar()
    {
        $atividadeComplementar = $this->registro->getAtividadeComplementar();
        $aee = $this->registro->getAtendimentoAEE();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Tipo de atendimento - Atividade complementar';
        $msgAtendimento = '"Atendimento educacional especializado - AEE"';

        if (!$this->isPreenchido($atividadeComplementar)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($atividadeComplementar, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($atividadeComplementar == 1 && $aee == 1) {
            $this->log(
                sprintf(
                    'O campo "%s" não pode ser preenchido com "Sim" quando o campo %s for preenchido com "Sim".',
                    $campo,
                    $msgAtendimento
                )
            );
        }

        if ($mediacao != 1 && $atividadeComplementar == 1) {
            $mediacaoAee = '"Tipo de mediação didático-pedagógica - AEE"';
            $this->log(
                sprintf(
                    'O campo "%s" não pode ser preenchido com "Sim" quando o campo %s for preenchido com "Sim".',
                    $campo,
                    $mediacaoAee
                )
            );
        }
    }

    private function validarAtendimentoAEE()
    {
        $aee = $this->registro->getAtendimentoAEE();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Tipo de atendimento - Atendimento educacional especializado (AEE)';

        if (!$this->isPreenchido($aee)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($aee, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($mediacao != 1 && $aee == 1) {
            $tipoMediacao = '"Tipo de mediação didático-pedagógica - AEE"';
            $this->log(
                sprintf(
                    'O campo "%s" não pode ser preenchido com "Sim" quando o campo %s for preenchido com "Sim".',
                    $campo,
                    $tipoMediacao
                )
            );
        }
    }

    private function validarTipoAtividadeComplementar()
    {
        $atividadeComplementar = $this->registro->getAtividadeComplementar();

        $campos = array();
        $campos[] = $this->registro->getCodigo1();
        $campos[] = $this->registro->getCodigo2();
        $campos[] = $this->registro->getCodigo3();
        $campos[] = $this->registro->getCodigo4();
        $campos[] = $this->registro->getCodigo5();
        $campos[] = $this->registro->getCodigo6();

        // Remover códigos que não foram preenchidos...
        $campos = array_diff($campos, array(null));

        if ($atividadeComplementar == 1 && count($campos) < 1) {
            $this->log('O campo "Atividade complementar" foi preenchido com "Sim", ' .
                'porém a turma não informou o tipo de atividade complementar.');
        }

        // Se algum dos códigos estava duplicado...
        if (count(array_unique($campos)) < count($campos)) {
            $this->log('"Tipo de atividade complementar" não foi preenchido corretamente. ' .
                'Não pode haver dois códigos do tipo de atividade iguais.');
        }
    }

    private function validarCodigo1()
    {
        $codigo = $this->registro->getCodigo1();
        $atividadeComplementar = $this->registro->getAtividadeComplementar();
        $campo = 'Tipo de atividade complementar - Código 1';

        if ($this->isPreenchido($codigo) && $atividadeComplementar != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarCodigo2()
    {
        $codigo = $this->registro->getCodigo2();
        $atividadeComplementar = $this->registro->getAtividadeComplementar();
        $campo = 'Tipo de atividade complementar - Código 2';

        if ($this->isPreenchido($codigo) && $atividadeComplementar != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarCodigo3()
    {
        $codigo = $this->registro->getCodigo3();
        $atividadeComplementar = $this->registro->getAtividadeComplementar();
        $campo = 'Tipo de atividade complementar - Código 3';

        if ($this->isPreenchido($codigo) && $atividadeComplementar != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarCodigo4()
    {
        $codigo = $this->registro->getCodigo4();
        $atividadeComplementar = $this->registro->getAtividadeComplementar();
        $campo = 'Tipo de atividade complementar - Código 4';

        if ($this->isPreenchido($codigo) && $atividadeComplementar != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarCodigo5()
    {
        $codigo = $this->registro->getCodigo5();
        $atividadeComplementar = $this->registro->getAtividadeComplementar();
        $campo = 'Tipo de atividade complementar - Código 5';

        if ($this->isPreenchido($codigo) && $atividadeComplementar != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarCodigo6()
    {
        $codigo = $this->registro->getCodigo6();
        $atividadeComplementar = $this->registro->getAtividadeComplementar();
        $campo = 'Tipo de atividade complementar - Código 6';

        if ($this->isPreenchido($codigo) && $atividadeComplementar != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }
    }

    private function validarLocalFuncionamentoDiferenciado()
    {
        $localDiferenciado = $this->registro->getLocalFuncionamentoDiferenciado();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $predio = $this->registro10->getPredioEscolar();
        $salaOutraEscola = $this->registro10->getSalaOutraEscola();
        $galpao = $this->registro10->getGalpaoRanchoPaiolBarracao();
        $outroLocal = $this->registro10->getOutroLocal();
        $unidadeSocioEducativa = $this->registro10->getUnidadeAtendimentoSocioeducativa();
        $unidadePrisional = $this->registro10->getUnidadePrisional();
        $campo = 'Local de funcionamento diferenciado da turma';

        if (in_array($mediacao, array(1, 2)) && !$this->isPreenchido($localDiferenciado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($mediacao, array(1, 2)) && $this->isPreenchido($localDiferenciado)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        $opcoesValidas = [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 70, 71, 73, 74, 64, 67, 68];
        if ($mediacao === 3 && !in_array($localDiferenciado, $opcoesValidas)) {
            $opcoes = implode(', ', $opcoesValidas);
            $txtCondicao = 'quando o campo "Mediação didático-pedagógica" for preenchido com 3 (Educação a distância)';
            $this->log(sprintf('O campo "%s" deve ser preenchido com "%s" %s.', $campo, $opcoes, $txtCondicao));
        }

        if (!$this->isPreenchido($localDiferenciado)) {
            return;
        }

        if (!in_array($localDiferenciado, array(0, 1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($localDiferenciado == 0 && $predio == 0 && $salaOutraEscola == 0 && $galpao == 0 && $outroLocal == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "A turma não está em local de ' .
                'funcionamento diferenciado" quando os campos "Prédio escolar", ' .
                '"Sala(s) em outra escola", "Galpão/ rancho/ paiol/ barracão" e ' .
                '"Outros" do registro 10 forem preenchidos com "Não".', $campo));
        }

        if ($localDiferenciado == 1 && $predio == 0 && $salaOutraEscola == 0 && $galpao == 0 && $outroLocal == 0) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sala anexa" quando os campos ' .
                '"Prédio escolar", "Sala(s) em outra escola", "Galpão/ rancho/ paiol/ barracão" e "Outros" do ' .
                'registro 10 forem preenchidos com "Não".', $campo));
        }

        if ($localDiferenciado == 2 && $unidadeSocioEducativa == 0) {
            $this->log(
                sprintf(
                    'O campo "%s" não pode ser preenchido com "Unidade de atendimento socioeducativo" ' .
                    'quando o campo "Unidade de atendimento Socioeducativo" do registro 10 for preenchido com "Não".',
                    $campo
                )
            );
        }

        if ($localDiferenciado == 3 && $unidadePrisional == 0) {
            $this->log(sprintf('O campo não pode ser preenchido com "Unidade prisional" quando o campo ' .
                '"Unidade prisional" do registro 10 for preenchido com "Não".', $campo));
        }
    }

    private function validarModalidade()
    {
        $modalidade = $this->registro->getModalidade();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Modalidade';

        if ($escolarizacao == 1 && !$this->isPreenchido($modalidade)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($escolarizacao != 1 && $this->isPreenchido($modalidade)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($modalidade)) {
            return;
        }

        if (!in_array($modalidade, array(1, 2, 3, 4))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($mediacao == 2 && !in_array($modalidade, [2, 3])) {
            $this->log(
                sprintf(
                    'O campo "%s" deve ser preenchido com "Educação especial" ou "EJA" quando o campo '.
                    '"Mediação didático-pedagógica" for preenchido com "Semipresencial".',
                    $campo
                )
            );
        }

        if ($mediacao == 3 && !in_array($modalidade, array(1, 3, 4))) {
            $this->log(
                sprintf('O campo "%s" deve ser preenchido com "Ensino regular", "EJA" ou "Educação profissional" ' .
                    'quando o campo "Mediação didático-pedagógica" for preenchido com "Educação a distância".', $campo)
            );
        }
    }

    private function validarEtapa()
    {
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $modalidade = $this->registro->getModalidade();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $local = $this->registro->getLocalFuncionamentoDiferenciado();
        $atividadeComplementar = $this->registro->getAtividadeComplementar();
        $campo = 'Etapa';

        if ($escolarizacao == 1 && !$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($escolarizacao != 1 && $this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($etapa)) {
            return;
        }

        switch ($modalidade) {
            case 1:
                $etapasValidas = array(1, 2, 3, 14, 15, 16, 17, 18, 19, 20,21, 22, 23, 25, 26, 27, 28, 29, 35, 36, 37,
                    38, 41, 56);
                break;
            case 2:
                $etapasValidas = array(1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 30, 31,
                    32, 33, 34, 35, 36, 37, 38, 41, 56, 39, 40, 69, 70, 71, 72, 73, 74, 64, 67, 68);
                break;
            case 3:
                $etapasValidas = array(69, 70, 71, 72);
                break;
            case 4:
                $etapasValidas = array(30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67, 68);
                break;
            default:
                $etapasValidas = array();
        }
        if (!empty($etapasValidas)) {
            $modalidades = array(
                1 => 'Ensino regular',
                2 => 'Educação especial',
                3 => 'EJA',
                4 => 'Educação profissional'
            );

            if (!in_array($etapa, $etapasValidas)) {
                $this->log(sprintf('O campo "%s" deve ser preenchido com %s quando o campo "Modalidade" for ' .
                    'preenchido com "%s".', $campo, join(", ", $etapasValidas), $modalidades[$modalidade]));
            }
        }

        switch ($mediacao) {
            case 2:
                $etapasValidas = array(69, 70, 71, 72);
                break;
            case 3:
                $etapasValidas = array(30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 70, 71, 73, 74, 64, 67, 68);
                break;
            default:
                $etapasValidas = array();
        }
        if (!empty($etapasValidas)) {
            $mediacoes = array(
                3 => 'Educação a distância'
            );

            if (!in_array($etapa, $etapasValidas)) {
                $this->log(
                    sprintf('O campo "%s" deve ser preenchido com %s quando o campo "mediação ' .
                    'didático-pedagógica" for preenchido com "%s".', $campo, join(", ", $etapasValidas)),
                    $mediacoes[$mediacao]
                );
            }
        }

        if (in_array($local, array(2, 3)) && in_array($etapa, array(1, 2, 3, 56))) {
            $this->log(
                sprintf('O campo "%s" não pode ser preenchido com educação infantil quando o campo ' .
                    '"Tipo de Atendimento" for preenchido com "Unidade de internação socioeducativa" ou ' .
                    '"Unidade prisional".', $campo)
            );
        }

        if ($etapa != 1) {
            // TODO: Validar regra 10 do campo 29
        }

        if ($atividadeComplementar == 1 &&
            in_array($etapa, array(1, 2, 3, 39, 40, 56, 64, 67, 68, 69, 70, 71, 72, 73, 74))) {
            $this->log(
                sprintf(
                    'O campo "%s" não pode ser preenchido com 1, 2, 3, 39, 40, 56, 64, 67, 68, 69, 70, 71, ' .
                    '72, 73 ou 74 quando o campo "Atividade complementar" for preenchido com "Sim".',
                    $campo
                )
            );
        }

        if ($mediacao == 2 && !in_array($etapa, [69, 70, 71, 72])) {
            $this->log(
                sprintf(
                    'O campo "%s" deve ser preenchido com 69, 70, 71 ou 72 quando o campo ' .
                    '"Mediação didático-pedagógica" for preenchido com "Semipresencial".',
                    $campo
                )
            );
        }
    }

    private function validarCodigoCurso()
    {
        $curso = $this->registro->getCodigoCurso();
        $etapa = $this->registro->getEtapaCenso();
        $campo = 'Código Curso';

        if (!$this->isPreenchido($curso) && in_array($etapa, array(30, 31, 32, 33, 34, 39, 40, 64, 74))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($curso) && !in_array($etapa, array(30, 31, 32, 33, 34, 39, 40, 64, 74))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }
    }

    private function validarQuimica()
    {
        $materia = $this->registro->getQuimica();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 1. Química';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }

        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarFisica()
    {
        $materia = $this->registro->getFisica();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 2. Física';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarMatematica()
    {
        $materia = $this->registro->getMatematica();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 3. Matemática';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarBiologia()
    {
        $materia = $this->registro->getBiologia();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 4. Biologia';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarCiencias()
    {
        $materia = $this->registro->getCiencias();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 5. Ciências';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarLiteraturaPortuguesa()
    {
        $materia = $this->registro->getLiteraturaPortuguesa();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 6. Língua/Literatura Portuguesa';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarLiteraturaEstrangeiraIngles()
    {
        $materia = $this->registro->getLiteraturaEstrangeiraIngles();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 7. Língua/Literatura Estrangeira - Inglês';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarLiteraturaEstrangeiraEspanhol()
    {
        $materia = $this->registro->getLiteraturaEstrangeiraEspanhol();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 8. Língua/Literatura Estrangeira - Espanhol';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarLiteraturaEstrangeiraOutra()
    {
        $materia = $this->registro->getLiteraturaEstrangeiraOutra();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 9. Língua/Literatura Estrangeira - outra';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarArtes()
    {
        $materia = $this->registro->getArtes();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 10. Arte (Educação Artística, Teatro, Dança, ' .
            'Música, Artes Plásticas e outras)';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarEducacaoFisica()
    {
        $materia = $this->registro->getEducacaoFisica();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 11. Educação Física';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarHistoria()
    {
        $materia = $this->registro->getHistoria();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 12. História';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarGeografia()
    {
        $materia = $this->registro->getGeografia();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 13. Geografia';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarFilosofia()
    {
        $materia = $this->registro->getFilosofia();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 14. Filosofia';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarInformatica()
    {
        $materia = $this->registro->getInformatica();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 16. Informática/ Computação';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarCursosTecnicosProfissionais()
    {
        $materia = $this->registro->getCursosTecnicosProfissionais();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 17. Disciplinas dos Cursos Técnicos Profissionais';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarLibras()
    {
        $materia = $this->registro->getLibras();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 23. Libras';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarDisciplinasPedagogicas()
    {
        $materia = $this->registro->getDisciplinasPedagogicas();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 25. Disciplinas Pedagógicas';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarEnsinoReligioso()
    {
        $materia = $this->registro->getEnsinoReligioso();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 26. Ensino Religioso';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarLinguaIndigena()
    {
        $materia = $this->registro->getLinguaIndigena();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 27. Língua Indígena';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarEstudosSociais()
    {
        $materia = $this->registro->getEstudosSociais();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 28. Estudos Sociais';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarSociologia()
    {
        $materia = $this->registro->getSociologia();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 29. Sociologia';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarLiteraturaEstrangeiraFrances()
    {
        $materia = $this->registro->getLiteraturaEstrangeiraFrances();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 30. Língua/Literatura Estrangeira - Francês';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarPortuguesComoSegundaLingua()
    {
        $materia = $this->registro->getPortuguesComoSegundaLingua();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 31. Língua Portuguesa como Segunda Língua';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarEstagioSupervisionado()
    {
        $materia = $this->registro->getEstagioSupervisionado();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 32. Estágio Curricular Supervisionado';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    private function validarOutrasDisciplinas()
    {
        $materia = $this->registro->getOutrasDisciplinas();
        $etapa = $this->registro->getEtapaCenso();
        $escolarizacao = $this->registro->getEscolarizacao();
        $mediacao = $this->registro->getTipoMediacaoDidaticoPedagogica();
        $campo = 'Áreas do conhecimento/componentes curriculares - 99. Outras disciplinas';

        if (!$this->isPreenchido($materia) && $escolarizacao == 1 && !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($materia) && $escolarizacao == 0) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($materia) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($materia)) {
            return;
        }
        if (!$this->isPreenchido($etapa)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($materia, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($materia == 2 && $mediacao == 3) {
            $this->log(sprintf('O campo "%s" não pode ser preenchido com "Sim, oferece a área do ' .
                'conhecimento/componente curricular sem docente vinculado" quando o campo "Tipo de mediação ' .
                'didático-pedagógica" for preenchido com "Educação a distância".', $campo));
        }
    }

    /**
     * @param Registro50[] $registros
     */
    public function setRegistros50(array $registros)
    {
        $this->registros50 = $registros;
    }

    /**
     * @throws Exception
     */
    private function validarRegrasGerais()
    {
        $registro20 = $this->registro;
        $turmaPossuiProfissional = array_filter(
            $this->registros50,
            function (Registro50 $registro50) use ($registro20) {
                return $registro50->getCodigoTurma() == $registro20->getCodigoTurma();
            }
        );

        $mensagem = "Regras gerais:";
        if (count($turmaPossuiProfissional) === 0) {
            $this->log(
                sprintf(
                    '"%s" Turma informada sem profissional escolar em sala de aula vinculado a ela.',
                    $mensagem
                )
            );
        }

        $turmaPossuiAluno = array_filter($this->registros60, function (Registro60 $registro60) use ($registro20) {
            return $registro60->getCodigoTurma() == $registro20->getCodigoTurma();
        });

        if (count($turmaPossuiAluno) === 0) {
            $this->log(sprintf('"%s" Turma informada sem aluno(a) vinculado a ela.', $mensagem));
        }
    }

    /**
     * @param Registro60[] $registros
     */
    public function setRegistros60(array $registros)
    {
        $this->registros60 = $registros;
    }
}
