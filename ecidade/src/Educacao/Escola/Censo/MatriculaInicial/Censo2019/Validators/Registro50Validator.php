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

use ECidade\Educacao\Escola\Censo\Helpers\Turma;
use ECidade\Educacao\Escola\Censo\Log\LogMatriculaInicial;
use ECidade\Educacao\Escola\Censo\Registry\LogCensoRegistry;
use Exception;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro00;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro20;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro30;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro50;
use ECidade\Educacao\Escola\Censo\Helpers\Pessoa;

class Registro50Validator
{
    /**
     * @var Registro50
     */
    private $registro;

    /**
     * @var Registro00
     */
    private $registro00;

    /**
     * @var Registro20
     */
    private $registro20;

    /**
     * @var Registro30
     */
    private $registro30;

    public function setRegistro(Registro50 $registro)
    {
        $this->registro = $registro;
    }

    public function setRegistro00(Registro00 $registro00)
    {
        $this->registro00 = $registro00;
    }

    public function setRegistro20(Registro20 $registro20)
    {
        $this->registro20 = $registro20;
    }

    public function setRegistro30(Registro30 $registro30)
    {
        $this->registro30 = $registro30;
    }

    /**
     * Diz se o campo foi preenchido
     * @return boolean
     */
    private function isPreenchido($campo)
    {
        return $campo !== "" && !is_null($campo);
    }

    /**
     * @param $dado
     * @throws Exception
     */
    private function log($dado)
    {
        $pessoa = Pessoa::decodeCodigoProfissional($this->registro->getCodigoPessoa());

        if (!empty($this->registro30)) {
            $pessoa = "{$this->registro30->getNome()} - $pessoa";
        }

        $turma = '';
        if (!empty($this->registro20)) {
            $turma = sprintf(
                "Turma %s - %s",
                Turma::decodeCodigoTurma($this->registro20->getCodigoTurma()),
                $this->registro20->getNomeTurma()
            );
        }

        $dado = "{$pessoa} {$turma}: {$dado}";

        $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
        $log->add(LogMatriculaInicial::REGISTRO50, $dado);
    }

    public function validar()
    {
        if ($this->validarCodigoPessoa() || $this->validarCodigoTurma()) {
            return;
        }

        $this->validarTipoRegistro();
        $this->validarCodigoInepEscola();
        $this->validarCodigoInep();
        $this->validarCodigoTurmaInep();
        $this->validarFuncaoExerce();
        $this->validarRegimeContratacao();
        $this->validarComponentesCurriculares();
        $this->validarCodigo1();
        $this->validarCodigo2();
        $this->validarCodigo3();
        $this->validarCodigo4();
        $this->validarCodigo5();
        $this->validarCodigo6();
        $this->validarCodigo7();
        $this->validarCodigo8();
        $this->validarCodigo9();
        $this->validarCodigo10();
        $this->validarCodigo11();
        $this->validarCodigo12();
        $this->validarCodigo13();
        $this->validarCodigo14();
        $this->validarCodigo15();
        $this->validarCodigo16();
        $this->validarCodigo17();
        $this->validarCodigo18();
        $this->validarCodigo19();
        $this->validarCodigo20();
        $this->validarCodigo21();
        $this->validarCodigo22();
        $this->validarCodigo23();
        $this->validarCodigo24();
        $this->validarCodigo25();
    }

    private function validarTipoRegistro()
    {
        if ($this->registro->getTipoRegistro() !== '50') {
            $this->log('O tipo de registro deve ser "50".');
        }
    }

    private function validarCodigoInepEscola()
    {
        $dado = $this->registro->getCodigoInepEscola();
        $inepEscola = $this->registro00->getCodigoInep();
        $campo = 'Código de escola - Inep';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($inepEscola != $dado) {
            $this->log(sprintf('O campo "%s" está diferente do registro 00 antecedente.', $campo));
        }
    }

    private function validarCodigoPessoa()
    {
        $dado = $this->registro->getCodigoPessoa();
        $campo = 'Código da pessoa física no sistema próprio';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (strlen($dado) > 20) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (empty($this->registro30)) {
            $this->log(sprintf('"%s": Não há pessoa física com esse código nesta escola.', $campo));
            return true;
        }
    }

    private function validarCodigoInep()
    {
        $dado = $this->registro->getCodigoInep();
        $inepPessoa = $this->registro30->getCodigoInep();
        $campo = 'Identificação única (Inep)';

        if ($dado != $inepPessoa) {
            $this->log(sprintf(
                'O campo "%s" está diferente da "Identificação Única (Inep)" do registro 30 correspondente',
                $campo
            ));
        }
    }

    private function validarCodigoTurma()
    {
        $dado = $this->registro->getCodigoTurma();
        $campo = 'Código da Turma na Entidade/Escola';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (empty($this->registro20)) {
            $this->log(sprintf('"%s": Não há turma com esse código nesta escola.', $campo));
            return true;
        }
    }

    private function validarCodigoTurmaInep()
    {
        $dado = $this->registro->getCodigoTurmaInep();
        $campo = 'Código da turma no INEP';

        if ($this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }
    }

    private function validarFuncaoExerce()
    {
        $dado = $this->registro->getFuncaoExerce();
        $mediacao = $this->registro20->getTipoMediacaoDidaticoPedagogica();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $atividadeComplementar = $this->registro20->getAtividadeComplementar();
        $isItinerario = $this->registro20->getItinerarioFormativo() == 1;
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Função que exerce na escola/Turma';

        if ($dado == 9 && !$isItinerario && !in_array($etapa, [30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67, 68])) {
            $log = "O campo %s não pode ser preenchido com 9 (Instrutor da Educação Profissional)
                quando a estrutura curricular da turma não for itinerário formativo e a etapa da turma não for
                30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67 e 68.";
                $this->log(sprintf($log, $campo));
        }

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($dado, array(1, 2, 3, 4, 5, 6, 7, 8, 9))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (in_array($dado, array(1, 2, 3, 4, 7, 8)) && !in_array($mediacao, [1, 3])) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Docente", "Auxiliar/Assistente educacional", '.
                '"Profissional/Monitor de atividade complementar", "Tradutor-Intérprete de LIBRAS", '.
                '"Guia-Intérprete de Libras" ou "Profissional de apoio escolar para aluno(a)s com deficiência" quando'.
                ' o tipo de mediação didático-pedagógica da turma não for "presencial" ou "semipresencial".',
                $campo
            ));
        }

        if ($dado == 2 && $escolarizacao != 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Auxiliar/Assistente educacional" '.
                'quando o tipo de atendimento da turma não for de "escolarização".',
                $campo
            ));
        }

        if ($dado == 3 && $atividadeComplementar != 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Profissional/Monitor de atividade complementar" '.
                'quando o tipo de atendimento da turma não for de "atividade complementar".',
                $campo
            ));
        }

        if ($dado == 5 && $mediacao != 3) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Profissional/Monitor de atividade '.
                'complementar" quando o tipo de atendimento da turma não for de "atividade complementar".',
                $campo
            ));
        }

        if ($dado == 6 && $mediacao != 3) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Docente tutor" quando o tipo de '.
                'mediação didático-pedagógica da turma não for preenchido com "Educação a distância".',
                $campo
            ));
        }

        /**
         * @todo: Validar regras 8, 9 do campo 7
         */
    }

    private function validarRegimeContratacao()
    {
        $dado = $this->registro->getRegimeContratacao();
        $funcao = $this->registro->getFuncaoExerce();
        $dependencia = $this->registro00->getDependenciaAdministrativa();
        $campo = 'Situação Funcional/Regime de contratação/Tipo de vínculo';

        if (!$this->isPreenchido($dado) && in_array($funcao, array(1, 5, 6)) &&
            in_array($dependencia, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5, 6))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && !in_array($dependencia, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2, 3, 4))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarComponentesCurriculares()
    {
        $campos = array();
        $campos[] = $this->registro->getCodigo1();
        $campos[] = $this->registro->getCodigo2();
        $campos[] = $this->registro->getCodigo3();
        $campos[] = $this->registro->getCodigo4();
        $campos[] = $this->registro->getCodigo5();
        $campos[] = $this->registro->getCodigo6();
        $campos[] = $this->registro->getCodigo7();
        $campos[] = $this->registro->getCodigo8();
        $campos[] = $this->registro->getCodigo9();
        $campos[] = $this->registro->getCodigo10();
        $campos[] = $this->registro->getCodigo11();
        $campos[] = $this->registro->getCodigo12();
        $campos[] = $this->registro->getCodigo13();
        $campos[] = $this->registro->getCodigo14();
        $campos[] = $this->registro->getCodigo15();
        $campos = array_diff($campos, array(null));

        // Se existe algum valor repetido...
        if (count($campos) != count(array_unique($campos))) {
            $this->log(
                '"Áreas do conhecimento/componentes curriculares que o profissional escolar em sala de aula '.
                'leciona" não foram preenchidas corretamente. Não podem ser informadas duas Áreas do '.
                'conhecimento/componentes curriculares iguais.'
            );
        }
    }

    private function validarCodigo1()
    {
        $dado = $this->registro->getCodigo1();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 1';

        if (!$this->isPreenchido($dado) && in_array($funcao, array(1, 5)) && $escolarizacao == 1 &&
            !in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 6
         */
    }

    private function validarCodigo2()
    {
        $dado = $this->registro->getCodigo2();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 2';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo3()
    {
        $dado = $this->registro->getCodigo3();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 3';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo4()
    {
        $dado = $this->registro->getCodigo4();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 4';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo5()
    {
        $dado = $this->registro->getCodigo5();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 5';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo6()
    {
        $dado = $this->registro->getCodigo6();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 6';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo7()
    {
        $dado = $this->registro->getCodigo7();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 7';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo8()
    {
        $dado = $this->registro->getCodigo8();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 8';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo9()
    {
        $dado = $this->registro->getCodigo9();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 9';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo10()
    {
        $dado = $this->registro->getCodigo10();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 10';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo11()
    {
        $dado = $this->registro->getCodigo11();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 11';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo12()
    {
        $dado = $this->registro->getCodigo12();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 12';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo13()
    {
        $dado = $this->registro->getCodigo13();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 13';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo14()
    {
        $dado = $this->registro->getCodigo14();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 14';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }

    private function validarCodigo15()
    {
        $dado = $this->registro->getCodigo15();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo16()
    {
        $dado = $this->registro->getCodigo16();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo17()
    {
        $dado = $this->registro->getCodigo17();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo18()
    {
        $dado = $this->registro->getCodigo18();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo19()
    {
        $dado = $this->registro->getCodigo19();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo20()
    {
        $dado = $this->registro->getCodigo20();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo21()
    {
        $dado = $this->registro->getCodigo21();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo22()
    {
        $dado = $this->registro->getCodigo22();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo23()
    {
        $dado = $this->registro->getCodigo23();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo24()
    {
        $dado = $this->registro->getCodigo24();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
    private function validarCodigo25()
    {
        $dado = $this->registro->getCodigo24();
        $funcao = $this->registro->getFuncaoExerce();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Áreas do conhecimento/componentes curriculares - Código 15';

        if ($this->isPreenchido($dado) && !in_array($funcao, array(1, 5))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && in_array($etapa, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        /**
         * @todo: Validar regra 5
         */
    }
}
