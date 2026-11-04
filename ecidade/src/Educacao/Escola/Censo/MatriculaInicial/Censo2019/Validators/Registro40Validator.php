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
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro30;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro40;

class Registro40Validator
{
    /**
     * @var Registro40
     */
    private $registro;

    /**
     * @var Registro00
     */
    private $registro00;

    /**
     * @var Registro30
     */
    private $registro30;

    public function setRegistro(Registro40 $registro)
    {
        $this->registro = $registro;
    }

    public function setRegistro00(Registro00 $registro00)
    {
        $this->registro00 = $registro00;
    }

    public function setRegistro30(Registro30 $registro30)
    {
        $this->registro30 = $registro30;
    }

    /**
     * @param $dado
     * @throws Exception
     */
    private function log($dado)
    {
        $dado = "{$this->registro30->getNome()}: $dado";
        $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
        $log->add(LogMatriculaInicial::REGISTRO40, $dado);
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
     * Diz se uma string contém somente os caracteres informados
     * @param string $str A string
     * @param array|string $only Os Caracteres permitidos
     * @return bool
     */
    private function containsOnly($str, $only)
    {
        // Se após a remoção de todos os caracteres válidos a string
        // está vazia, é por que ela não tinha caracteres inválidos
        $res = str_replace($only, '', mb_strtoupper($str));
        return !$this->isPreenchido($res);
    }

    public function validar()
    {
        $this->validarTipoRegistro();
        $this->validarCodigoInepEscola();
        $this->validarCodigoPessoa();
        $this->validarCodigoInep();
        $this->validarCargo();
        $this->validarCriterioAcesso();
//        $this->validarEspecificacaoCriterioAcesso();
        $this->validarRegimeContratacao();
    }

    private function validarTipoRegistro()
    {
        if ($this->registro->getTipoRegistro() !== '40') {
            $this->log('O tipo de registro deve ser "40".');
        }
    }

    private function validarCodigoInepEscola()
    {
        $dado = $this->registro->getCodigoInepEscola();
        $inepAnterior = $this->registro00->getCodigoInep();
        $campo = 'Código de escola - Inep';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($dado != $inepAnterior) {
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

        if (empty($this->registro30)) {
            $this->log(sprintf('"%s": Não há pessoa física com esse código nesta escola.', $campo));
        }

        // TODO: Validar regra 4 do campo 3
    }

    private function validarCodigoInep()
    {
        $dado = $this->registro->getCodigoInep();
        $inepPessoa = $this->registro30->getCodigoInep();
        $campo = 'Identificação única (Inep)';

        if ($dado != $inepPessoa) {
            $this->log(sprintf(
                'O campo "%s" está diferente da "Identificação Única (Inep)" do registro 30 '.
                'correspondente.',
                $campo
            ));
        }
    }

    private function validarCargo()
    {
        $dado = $this->registro->getCargo();
        $campo = 'Cargo';

        if (!$this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!in_array($dado, array(1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCriterioAcesso()
    {
        $dado = $this->registro->getCriterioAcesso();
        $cargo = $this->registro->getCargo();
        $situacaoFuncionamento = (int)$this->registro00->getSituacaoFuncionamento();
        $dependencia = $this->registro00->getDependenciaAdministrativa();
        $campo = 'Critério de acesso ao cargo/função';

        if (!$this->isPreenchido($dado) && $cargo == 1 && $situacaoFuncionamento === 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $cargo != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $situacaoFuncionamento !== 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2, 3, 4, 5, 6, 7))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dado == 1 && in_array($dependencia, array(1, 2, 3))) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com "Ser proprietário(a) ou sócio(a)-proprietário(a) '.
                'da escola" quando o campo "Dependência administrativa" for preenchido com "Federal", "Estadual" '.
                'ou "Municipal".',
                $campo
            ));
        }

        if (in_array($dado, array(4, 5, 6)) && $dependencia == 4) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com 4, 5, 6 quando o campo "Dependência administrativa" '.
                'for preenchido com "Privada".',
                $campo
            ));
        }
    }

    private function validarEspecificacaoCriterioAcesso()
    {
        $dado = $this->registro->getEspecificacaoCriterioAcesso();
        $criterio = $this->registro->getCriterioAcesso();
        $campo = 'Especificação do critério de acesso';

        if (!$this->isPreenchido($dado) && $criterio == 7) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $criterio != 7) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (strlen($dado) > 100) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        $validos = array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U',
            'V', 'W', 'X', 'Y', 'Z', ' ', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'ª', 'º', '-'
        );
        if (!$this->containsOnly($dado, $validos)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarRegimeContratacao()
    {
        $dado = $this->registro->getRegimeContratacao();
        $cargo = $this->registro->getCargo();
        $situacao = $this->registro00->getSituacaoFuncionamento();
        $dependencia = $this->registro00->getDependenciaAdministrativa();
        $campo = 'Situação Funcional/Regime de contratação/Tipo de vínculo';

        if (!$this->isPreenchido($dado) && $cargo == 1 && $situacao == 1 && in_array($dependencia, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $cargo != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $situacao != 1) {
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
    public function semGestor()
    {
        $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
        $log->add(LogMatriculaInicial::REGISTRO40, 'Não foi informado nenhum gestor.');
    }
}
