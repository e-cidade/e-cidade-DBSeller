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
use ECidade\Educacao\Escola\Censo\Helpers\Turma;
use ECidade\Educacao\Escola\Censo\Log\LogMatriculaInicial;
use ECidade\Educacao\Escola\Censo\Registry\LogCensoRegistry;
use ECidade\Enum\Educacao\Secretaria\ComposicaoItinerarioFormativoIntegradoEnum;
use ECidade\Enum\Educacao\Secretaria\TipoItinerarioFormativoEnum;
use Exception;

use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro00;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro20;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro30;
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro60;
use ECidade\Educacao\Escola\Censo\Helpers\Pessoa;

class Registro60Validator
{
    /**
     * @var Registro60
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

    public function setRegistro(Registro60 $registro)
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
        $pessoa = Pessoa::decodeCodigoAluno($this->registro->getCodigoPessoa());

        if (!empty($this->registro30)) {
            $pessoa = "{$pessoa} - {$this->registro30->getNome()}";
        }

        $dado = "{$pessoa}: $dado";

        $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
        $log->add(LogMatriculaInicial::REGISTRO60, $dado);
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
        $this->validarCodigoMatricula();
        $this->validarEtapaTurmaMultietapa();
        $this->validarTiposAee();
        $this->validarDesenvolvimentoFuncoesCognitivas();
        $this->validarDesenvolvimentoVidaAutonoma();
        $this->validarEnriquecimentoCurricular();
        $this->validarEnsinoInformaticaAcessivel();
        $this->validarEnsinoLibras();
        $this->validarEnsinoPortuguesaSegundaLingua();
        $this->validarEnsinoCalculoSoroban();
        $this->validarEnsinoSistemaBraille();
        $this->validarEnsinoTecnicasOrientacaoMobilidade();
        $this->validarEnsinoCAA();
        $this->validarEnsinoRecursosOpticosNaoOpticos();
        $this->validarRecebeEscolarizacaoEspecial();
        $this->validarTransporteEscolarPublico();
        $this->validarResponsavelTransporteEscolar();
        $this->validarTipoVeiculo();
        $this->validarBicicleta();
        $this->validarMicroonibus();
        $this->validarOnibus();
        $this->validarTracaoAnimal();
        $this->validarVansKombis();
        $this->validarOutro();
        $this->validarAquaviarioAte5();
        $this->validarAquaviarioEntre5A15();
        $this->validarAquaviarioEntre15A35();
        $this->validarAquaviarioAcima35();
        $this->validarAreasItinerarioFormativo();
        $this->validarComposicaoItinerarioFormativoIntegrado();
    }

    private function validarTipoRegistro()
    {
        if ($this->registro->getTipoRegistro() !== '60') {
            $this->log('O tipo de registro deve ser "60".');
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

        if ($inepAnterior != $dado) {
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
            if ($this->registro20->getAtividadeComplementar() === 1) {
                $turma = Turma::decodeCodigoTurma($this->registro20->getCodigoTurma());
                $this->log(sprintf(
                    'O aluno de código %s esta vínculado na turma "%s" de atividade complementar mais não '.
                    'esta presente no registro 30',
                    Pessoa::decodeCodigoAluno($this->registro->getCodigoPessoa()),
                    "$turma -  {$this->registro20->getNomeTurma()}"
                ));
                return true;
            }

            $this->log(sprintf('"%s": Não há pessoa física com esse código nesta escola.', $campo));
            return true;
        }
    }

    private function validarCodigoInep()
    {
        if (empty($this->registro30)) {
            return;
        }
        $dado = $this->registro->getCodigoInep();
        $inepAnterior = $this->registro30->getCodigoInep();
        $campo = 'Identificação única (Inep)';

        if ($dado != $inepAnterior) {
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

    private function validarCodigoMatricula()
    {
        $dado = $this->registro->getCodigoMatricula();
        $campo = 'Código da Matrícula do(a) aluno(a)';

        if ($this->isPreenchido($dado)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }
    }

    private function validarEtapaTurmaMultietapa()
    {
        $dado = $this->registro->getEtapaTurmaMultietapa();
        $etapa = $this->registro20->getEtapaCenso();
        $campo = 'Turma multi';

        if (!$this->isPreenchido($dado) && in_array($etapa, array(3, 22, 23, 72, 56, 64))) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if ($this->isPreenchido($dado) && !in_array($etapa, array(3, 22, 23, 72, 56, 64))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($dado, array(1, 2)) && $etapa == 3) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (!in_array($dado, array(14, 15, 16, 17, 18, 19, 20, 21, 41)) && in_array($etapa, array(22, 23))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (!in_array($dado, array(69, 70)) && $etapa == 72) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (!in_array($dado, array(1, 2, 14, 15, 16, 17, 18, 19, 20, 21, 41)) && $etapa == 56) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (!in_array($dado, array(39, 40)) && $etapa == 64) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarTiposAee()
    {
        $msg = '"Tipo de atendimento educacional especializado" não foi preenchido corretamente.';
        $atendimentoAee = $this->registro20->getAtendimentoAEE();

        $campos = array();
        $campos[] = $this->registro->getDesenvolvimentoFuncoesCognitivas();
        $campos[] = $this->registro->getDesenvolvimentoVidaAutonoma();
        $campos[] = $this->registro->getEnriquecimentoCurricular();
        $campos[] = $this->registro->getEnsinoInformaticaAcessivel();
        $campos[] = $this->registro->getEnsinoLibras();
        $campos[] = $this->registro->getEnsinoPortuguesaSegundaLingua();
        $campos[] = $this->registro->getEnsinoCalculoSoroban();
        $campos[] = $this->registro->getEnsinoSistemaBraille();
        $campos[] = $this->registro->getEnsinoTecnicasOrientacaoMobilidade();
        $campos[] = $this->registro->getEnsinoCAA();
        $campos[] = $this->registro->getEnsinoRecursosOpticosNaoOpticos();
        $campos = array_diff($campos, array(null));
        $campos = array_count_values($campos);

        if ($atendimentoAee == 1 && (!isset($campos[1]) || $campos[1] <= 0)) {
            $this->log(sprintf('%s Não podem ser informadas todas as opções com valor igual a 0 (Não).', $msg));
        }
    }

    private function validarDesenvolvimentoFuncoesCognitivas()
    {
        $dado = $this->registro->getDesenvolvimentoFuncoesCognitivas();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Desenvolvimento de funções cognitivas';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarDesenvolvimentoVidaAutonoma()
    {
        $dado = $this->registro->getDesenvolvimentoVidaAutonoma();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Desenvolvimento de vida autônoma';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnriquecimentoCurricular()
    {
        $dado = $this->registro->getEnriquecimentoCurricular();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Enriquecimento curricular';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnsinoInformaticaAcessivel()
    {
        $dado = $this->registro->getEnsinoInformaticaAcessivel();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Ensino da informática acessível';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnsinoLibras()
    {
        $dado = $this->registro->getEnsinoLibras();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Ensino da Língua Brasileira de Sinais (Libras)';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnsinoPortuguesaSegundaLingua()
    {
        $dado = $this->registro->getEnsinoPortuguesaSegundaLingua();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Ensino da Língua Portuguesa como Segunda Língua';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnsinoCalculoSoroban()
    {
        $dado = $this->registro->getEnsinoCalculoSoroban();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Ensino das técnicas do cálculo no Soroban';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnsinoSistemaBraille()
    {
        $dado = $this->registro->getEnsinoSistemaBraille();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Ensino de Sistema Braille';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnsinoTecnicasOrientacaoMobilidade()
    {
        $dado = $this->registro->getEnsinoTecnicasOrientacaoMobilidade();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Ensino de técnicas para orientação e mobilidade';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnsinoCAA()
    {
        $dado = $this->registro->getEnsinoCAA();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Ensino de uso da Comunicação Alternativa e ';
        $campo .= 'Aumentativa (CAA)';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEnsinoRecursosOpticosNaoOpticos()
    {
        $dado = $this->registro->getEnsinoRecursosOpticosNaoOpticos();
        $atendimentoAee = $this->registro20->getAtendimentoAEE();
        $campo = 'Tipo de atendimento educacional especializado - Ensino de uso de recursos ópticos e não ópticos';

        if (!$this->isPreenchido($dado) && $atendimentoAee == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $atendimentoAee != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarRecebeEscolarizacaoEspecial()
    {
        $dado = $this->registro->getRecebeEscolarizacaoEspecial();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $mediacao = $this->registro20->getTipoMediacaoDidaticoPedagogica();
        $localFuncionamentoDiferenciado = $this->registro20->getLocalFuncionamentoDiferenciado();
        $campo = 'Recebe escolarização em outro espaço (diferente da escola)';

        if (!$this->isPreenchido($dado) && $escolarizacao == 1 && $mediacao == 1 &&
            ($localFuncionamentoDiferenciado == 0 || $localFuncionamentoDiferenciado == 1)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $localFuncionamentoDiferenciado != 0
            && $localFuncionamentoDiferenciado != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $mediacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarTransporteEscolarPublico()
    {
        $dado = $this->registro->getTransporteEscolarPublico();
        $mediacao = $this->registro20->getTipoMediacaoDidaticoPedagogica();
        $escolarizacao = $this->registro20->getEscolarizacao();
        $paisResidencia = $this->registro30->getPaisResidencia();
        $campo = 'Transporte escolar público';

        if (!$this->isPreenchido($dado) && in_array($mediacao, array(1, 2)) &&
            $escolarizacao == 1 && $paisResidencia == 76) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $paisResidencia != 76) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && !in_array($mediacao, array(1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($this->isPreenchido($dado) && $escolarizacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarResponsavelTransporteEscolar()
    {
        $dado = $this->registro->getResponsavelTransporteEscolar();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Poder Público responsável pelo transporte escolar';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarTipoVeiculo()
    {
        $mensagem = 'Todas as opções de tipo de veículo utilizado no transporte escolar público';

        $campos = array();
        $campos[] = $this->registro->getBicicleta();
        $campos[] = $this->registro->getMicroonibus();
        $campos[] = $this->registro->getOnibus();
        $campos[] = $this->registro->getTracaoAnimal();
        $campos[] = $this->registro->getVansKombis();
        $campos[] = $this->registro->getOutro();
        $campos[] = $this->registro->getAquaviarioAte5();
        $campos[] = $this->registro->getAquaviarioEntre5A15();
        $campos[] = $this->registro->getAquaviarioEntre15A35();
        $campos[] = $this->registro->getAquaviarioAcima35();
        $campos = array_diff($campos, array(null));
        $respostas = array_count_values($campos);

        // Se todas as respostas forem 0...
        if (isset($respostas[0]) && count($campos) == $respostas[0]) {
            $this->log(sprintf('%s foram preenchidas com "Não"', $mensagem));
        }

        $campos = [];
        $campos[] = $this->registro->getBicicleta();
        $campos[] = $this->registro->getMicroonibus();
        $campos[] = $this->registro->getOnibus();
        $campos[] = $this->registro->getTracaoAnimal();
        $campos[] = $this->registro->getVansKombis();
        $campos[] = $this->registro->getOutro();
        $campos = array_diff($campos, [null]);
        $respostas = array_count_values($campos);

        // Se todas as respostas forem 1...
        if (isset($respostas[1]) && count($campos) == $respostas[1]) {
            $this->log(sprintf('%s rodoviário foram preenchidas com 1 "Sim".', $mensagem));
        }

        $campos = [];
        $campos[] = $this->registro->getAquaviarioAte5();
        $campos[] = $this->registro->getAquaviarioEntre5A15();
        $campos[] = $this->registro->getAquaviarioEntre15A35();
        $campos[] = $this->registro->getAquaviarioAcima35();
        $campos = array_diff($campos, [null]);
        $respostas = array_count_values($campos);
        // Se todas as respostas forem 1...
        if (isset($respostas[1]) && count($campos) == $respostas[1]) {
            $this->log(sprintf('%s aquaviário foram preenchidas com 1 "Sim".', $mensagem));
        }
    }

    private function validarBicicleta()
    {
        $dado = $this->registro->getBicicleta();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Rodoviário - Bicicleta';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarMicroonibus()
    {
        $dado = $this->registro->getMicroonibus();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Rodoviário - Microônibus';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarOnibus()
    {
        $dado = $this->registro->getOnibus();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Rodoviário - Ônibus';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarTracaoAnimal()
    {
        $dado = $this->registro->getTracaoAnimal();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Rodoviário ? Tração Animal';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarVansKombis()
    {
        $dado = $this->registro->getVansKombis();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Rodoviário - Vans/Kombis';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarOutro()
    {
        $dado = $this->registro->getOutro();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Rodoviário - Outro';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAquaviarioAte5()
    {
        $dado = $this->registro->getAquaviarioAte5();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Aquaviário - Capacidade de até 5 aluno(a)s';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAquaviarioEntre5A15()
    {
        $dado = $this->registro->getAquaviarioEntre5A15();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Aquaviário - Capacidade entre 5 a 15 ';
        $campo .= 'aluno(a)s';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAquaviarioEntre15A35()
    {
        $dado = $this->registro->getAquaviarioEntre15A35();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Aquaviário - Capacidade entre 15 a 35 ';
        $campo .= 'aluno(a)s';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAquaviarioAcima35()
    {
        $dado = $this->registro->getAquaviarioAcima35();
        $transportePublico = $this->registro->getTransporteEscolarPublico();
        $campo = 'Tipo de veículo utilizado no transporte escolar público - Aquaviário - Capacidade acima de 35 ';
        $campo .= 'aluno(a)s';

        if (!$this->isPreenchido($dado) && $transportePublico == 1) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            return;
        }

        if ($this->isPreenchido($dado) && $transportePublico != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($dado)) {
            return;
        }

        if (!in_array($dado, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarAreasItinerarioFormativo()
    {
        $isItinerario = $this->registro20->getItinerarioFormativo() == 1;
        if (!$isItinerario) {
            return;
        }
        $areas = $this->registro->getAreasItinerarioFormativo();

        if (empty($areas)) {
            $log = '"Área do itinerário formativo" não foi preenchido corretamente. ';
            $log .= 'Não podem ser informadas todas as opções com valor igual a 0 (Não).';
            $this->log($log);
            return;
        }

        if (count($areas) > 1) {
            $log = '"Área do itinerário formativo" não foi preenchido corretamente. ';
            $log .= 'Não podem ser informadas mais de uma opções com valor igual a 1 (Sim).';
            $this->log($log);
            return;
        }

        foreach ($areas as $key => $area) {
            $campo = $area;
            switch (intval($key)) {
                case TipoItinerarioFormativoEnum::LINGUAGEM_SUAS_TECNOLOGIAS:
                    $dado = $this->registro->getLiguagensTecnlogias();
                    break;
                case TipoItinerarioFormativoEnum::MATEMATICA_SUAS_TECNOLOGIAS:
                    $dado = $this->registro->getMatematicaTecnlogias();
                    break;
                case TipoItinerarioFormativoEnum::CIENCIAS_HUMANAS_SUAS_TECNOLOGIAS:
                    $dado = $this->registro->getCienciasHumanas();
                    break;
                case TipoItinerarioFormativoEnum::CIENCIAS_NATUREZA_SUAS_TECNOLOGIAS:
                    $dado = $this->registro->getCienciasNaturezaTecnlogias();
                    break;
                case TipoItinerarioFormativoEnum::ITINERARIO_FORMATIVO_INTEGRADO:
                    $dado = $this->registro->getItenarioFormativoIntegrado();
                    break;
                case TipoItinerarioFormativoEnum::FORMACAO_TECNICA_PROFISSIONAL:
                    $dado = $this->registro->getFormacaoTecnicaProfissional();
                    break;
            }


            $etapaRegistro20 = $this->registro20->getEtapaCenso();

            if (empty($dado) &&
                in_array($etapaRegistro20, [26, 27, 28, 31, 32, 33, 36, 37, 38, 71, 74]) && $isItinerario
            ) {
                $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
            }

            if (!in_array($etapaRegistro20, [25, 26, 27, 28, 30, 31, 32, 33, 35, 36, 37,  38, 71]) && $isItinerario) {
                $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            }

            if (!$isItinerario) {
                $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
            }

            if (!in_array($dado, [0, 1])) {
                $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            }
        }
    }

    public function validarComposicaoItinerarioFormativoIntegrado()
    {

        $isItinerario = $this->registro20->getItinerarioFormativo() == 1;
        if (!$isItinerario) {
            return;
        }
        $composicoes = $this->registro->getComposicaoItinerarioIntegrado();
        if (empty($composicoes)) {
            $log = '"Composição do itinerário formativo integrado" não foi preenchido corretamente.';
            $log .= 'Não podem ser informadas mais de três opções com valor igual a 0 (Não).';
            $this->log($log);
            return;
        }

        if (count($composicoes) > 4) {
            $log = '"Composição do itinerário formativo integrado" não foi preenchido corretamente.';
            $log .= 'Não podem ser informadas mais de quatro opções com valor igual a 1 (Sim).';
            $this->log($log);
            return;
        }

        foreach ($composicoes as $composicao) {
            foreach ($composicao['composicao'] as $key => $comp) {
                $campo = "Composição itinerário formativo integrado - ";
                $campo .= $comp;
                switch ($key) {
                    case ComposicaoItinerarioFormativoIntegradoEnum::LINGUAGEM_SUAS_TECNOLOGIAS:
                        $dado = $this->registro->getLiguagensTecnlogiasIntegrado();
                        break;
                    case ComposicaoItinerarioFormativoIntegradoEnum::MATEMATICA_SUAS_TECNOLOGIAS:
                        $dado = $this->registro->getMatematicaTecnlogiasIntegrado();
                        break;
                    case ComposicaoItinerarioFormativoIntegradoEnum::CIENCIAS_NATUREZA_SUAS_TECNOLOGIAS:
                        $dado = $this->registro->getCienciasNaturezaTecnlogiasIntegrado();
                        break;
                    case ComposicaoItinerarioFormativoIntegradoEnum::CIENCIAS_HUMANAS_SUAS_TECNOLOGIAS:
                        $dado = $this->registro->getCienciasHumanasIntegrado();
                        break;
                    case ComposicaoItinerarioFormativoIntegradoEnum::FORMACAO_TECNICA_PROFISSIONAL:
                        $dado = $this->registro->getFormacaoTecnicaProfissionalIntegrado();
                        break;
                }

                if (!$isItinerario) {
                    $this->log(
                        sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo)
                    );
                }

                if (!in_array($dado, [0, 1])) {
                    $this->log(
                        sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo)
                    );
                }

                if ($dado == ComposicaoItinerarioFormativoIntegradoEnum::FORMACAO_TECNICA_PROFISSIONAL) {
                    $tipoCurso = $this->registro->getCursoFormacaoTecnicaProfissionalIntegrado();
                    $concomitante = $this->registro->getItenarioConcomitanteIntegrado();
                    if (!in_array($tipoCurso, [1, 2])) {
                        $this->log(
                            sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo)
                        );
                    }

                    if (!in_array($concomitante, [0, 1])) {
                        $this->log(
                            sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo)
                        );
                    }
                }
            }
        }
    }
}
