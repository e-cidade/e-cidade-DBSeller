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
use ECidade\Educacao\Escola\Censo\MatriculaInicial\Censo2019\Model\Registro00;
use ECidade\Educacao\Escola\Censo\Registry\LogCensoRegistry;
use Exception;
use ECidade\Educacao\Escola\Censo\Censo;

class Registro00Validator
{
    /**
     * @var Registro00
     */
    private $registro;

    /**
     * @var Censo
     */
    private $censo;

    private $valido = true;

    private $caracteresValidosEndereco = array(
        'A',
        'B',
        'C',
        'D',
        'E',
        'F',
        'G',
        'H',
        'I',
        'J',
        'K',
        'L',
        'M',
        'N',
        'O',
        'P',
        'Q',
        'R',
        'S',
        'T',
        'U',
        'V',
        'W',
        'X',
        'Y',
        'Z',
        ' ',
        '0',
        '1',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        'ª',
        'º',
        '-',
        '?',
        '/',
        '.',
        ',');


    public function setRegistro(Registro00 $registro)
    {
        $this->registro = $registro;
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
        $log = LogCensoRegistry::get(LogCensoRegistry::MATRICULA_INICIAL);
        $log->add(LogMatriculaInicial::REGISTRO00, $dado);
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
        $this->validarTipoRegistro();
        $this->validarCodigoInep();
        $this->validarSituacaoFunctionamento();
        $this->validarDataInicioAnoLetivo();
        $this->validarDataFinalAnoLetivo();
        $this->validarNomeEscola();
        $this->validarCep();
        $this->validarMunicipio();
        $this->validarDistrito();
        $this->validarEndereco();
        $this->validarNumero();
        $this->validarComplemento();
        $this->validarBairro();
        $this->validarDdd();
        $this->validarTelefones();
        $this->validarTelefone();
        $this->validarOutroTelefone();
        $this->validarEmail();
        $this->validarCodigoOrgaoRegionalEnsino();
        $this->validarZonaEscola();
        $this->validarLocalizacaoDiferenciada();
        $this->validarDependenciaAdministrativa();
        $this->validarOrgaoEscolaPublica();
        $this->validarSecretariaEducacao();
        $this->validarSecretariaSeguranca();
        $this->validarSecretariaSaude();
        $this->validarOutroOrgaoAdministracaoPublica();
        $this->validarMantenedoraEscolaPrivada();
        $this->validarMantenedoraPrivada();
        $this->validarMantenedoraSindicatos();
        $this->validarMantenedoraOng();
        $this->validarMantenedoraInstituicaoSemFimLucrativo();
        $this->validarMantenedoraSistemaS();
        $this->validarMantenedoraOscip();
        $this->validarCategoriaEscolaPrivada();
        $this->validarCnpjMantenedoraPrincipal();
        $this->validarCnpjEscolaPrivada();
        $this->validarRegulamentacao();
        $this->validarEsferaAdministrativa();
        $this->validarEsferaFederal();
        $this->validarEsferaEstadual();
        $this->validarEsferaMunicipal();
        $this->validarUnidadeVinculada();
        $this->validarCodigoEscolaSede();
        $this->validarCodigoIES();
    }

    private function validarTipoRegistro()
    {
        if ($this->registro->getTipoRegistro() !== '00') {
            $this->log("Tipo de registro deve ser 00");
        }
    }


    private function validarCodigoInep()
    {
        $inep = $this->registro->getCodigoInep();
        $campo = 'Código INEP';
        if (!$this->isPreenchido($inep)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (strlen($inep) < 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isSomenteNumero($inep)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarSituacaoFunctionamento()
    {
        $situacao = $this->registro->getSituacaoFuncionamento();
        $campo = 'Situação de funcionamento';
        if (!$this->isPreenchido($situacao)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($situacao, array(1, 2, 3))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarDataInicioAnoLetivo()
    {
        $inicio = $this->registro->getDataIncicioAnoLetivo();
        $campo = 'Data de início do ano letivo';

        if ($this->registro->getSituacaoFuncionamento() === 1 && !$this->isPreenchido($inicio)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($this->registro->getSituacaoFuncionamento() != 1 && $this->isPreenchido($inicio)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($inicio)) {
            return;
        }

        list($dia, $mes, $ano) = sscanf($inicio, '%d/%d/%d');
        if (!checkdate($mes, $dia, $ano)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $dataCenso = $this->censo->getDataCenso();
        $dataInicial = DBDate::create($inicio);
        $bissexto = date('L', $dataCenso->getTimeStamp());

        if ($bissexto) {
            if (DBDate::getIntervaloEntreDatas($dataCenso, $dataInicial)->days > 366) {
                $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            }
        } else {
            if (DBDate::getIntervaloEntreDatas($dataCenso, $dataInicial)->days > 365) {
                $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            }
        }

        $proximoAno = $ano + 1;
        $dataProximoAno = new DBDate("01/01/{$proximoAno}");
        $bissexto = date('L', $dataProximoAno->getTimeStamp());

        if ($bissexto) {
            if (DBDate::getIntervaloEntreDatas($dataCenso, $dataInicial)->days > 366) {
                $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            }
        } else {
            if (DBDate::getIntervaloEntreDatas($dataCenso, $dataInicial)->days > 365) {
                $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
            }
        }
    }

    private function validarDataFinalAnoLetivo()
    {
        $final = $this->registro->getDataFinalAnoLetivo();
        $inicio = $this->registro->getDataIncicioAnoLetivo();
        $campo = 'Data de término do ano letivo';

        if ($this->registro->getSituacaoFuncionamento() === 1 && !$this->isPreenchido($final)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($this->registro->getSituacaoFuncionamento() != 1 && $this->isPreenchido($final)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($final)) {
            return;
        }

        list($dia, $mes, $ano) = sscanf($final, '%d/%d/%d');
        if (!checkdate($mes, $dia, $ano)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        $dataCenso = $this->censo->getDataCenso();
        $proximoAno = $ano + 1;
        $stringUltimaQuartaMaioAnoSubsequente = strtotime(
            sprintf('%s%s', $proximoAno, '-05-00 last wednesday of this month')
        );
        $ultimaQuartaMaioAnoSubsequente = date('Y-m-d', $stringUltimaQuartaMaioAnoSubsequente);
        $dataUltimaQuartaMaioAnoSubsequente = new DBDate($ultimaQuartaMaioAnoSubsequente);

        $dataInicial = DBDate::create($inicio);
        $dataFinal = DBDate::create($final);

        if ($dataFinal->getTimeStamp() < $dataCenso->getTimeStamp()) {
            $this->log(
                sprintf(
                    'O campo "%s" não pode ser preenchido com uma data anterior à data de referência do Censo Escolar.',
                    $campo
                )
            );
        }

        if ($dataFinal->getTimeStamp() > $dataUltimaQuartaMaioAnoSubsequente->getTimeStamp()) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido', $campo));
        }

        if ($dataFinal->getTimeStamp() <= $dataInicial->getTimeStamp()) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com uma data anterior ou igual à data do campo %s',
                '"Data de início do ano letivo".',
                $campo
            ));
        }
    }

    private function validarNomeEscola()
    {
        $nome = $this->registro->getNomeEscola();
        $campo = 'Nome da escola';

        if (!$this->isPreenchido($nome)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (strlen($nome) > 100) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        $validos = array(
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z',
            ' ',
            '0',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            'ª',
            'º',
            '-',
            '?');
        if (!$this->containsOnly($nome, $validos)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (strlen(str_replace(' ', '', $nome)) < 4) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }
    }

    private function validarCep()
    {
        $cep = $this->registro->getCep();
        $campo = 'CEP';

        if (!$this->isPreenchido($cep)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (strlen($cep) !== 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!ctype_digit($cep)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarMunicipio()
    {
        $municipio = $this->registro->getMunicipio();
        $campo = 'Municipio';

        if (!$this->isPreenchido($municipio)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        // FIXME: Regra 2 do campo 8 deveria ser processamento
    }

    private function validarDistrito()
    {
        $distrito = $this->registro->getDistrito();
        $campo = 'Distrito';

        if (!$this->isPreenchido($distrito)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        // FIXME: Regra 2 do campo 9 deveria ser processamento
    }

    private function validarEndereco()
    {
        $endereco = $this->registro->getEndereco();
        $campo = 'Endereço';

        if (!$this->isPreenchido($endereco)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (strlen($endereco) > 100) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        $validos = $this->caracteresValidosEndereco;
        if (!$this->containsOnly($endereco, $validos)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarNumero()
    {
        $numero = $this->registro->getNumero();
        $campo = 'Número';

        if (!$this->isPreenchido($numero)) {
            return;
        }

        if (strlen($numero) > 10) {
            $this->log(sprintf('O campo "%s" está maior que o especificado.', $campo));
        }

        $validos = $this->caracteresValidosEndereco;
        if (!$this->containsOnly($numero, $validos)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarComplemento()
    {
        $complemento = $this->registro->getComplemento();
        $campo = 'Complemento';

        if (!$this->isPreenchido($complemento)) {
            return;
        }

        if (strlen($complemento) > 20) {
            $this->log(sprintf('O campo "%s" está maior que o especificado.', $campo));
        }

        $validos = $this->caracteresValidosEndereco;
        if (!$this->containsOnly($complemento, $validos)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarBairro()
    {
        $bairro = $this->registro->getBairro();
        $campo = 'Bairro';

        if (!$this->isPreenchido($bairro)) {
            return;
        }

        if (strlen($bairro) > 50) {
            $this->log(sprintf('O campo "%s" está maior que o especificado.', $campo));
        }

        $validos = $this->caracteresValidosEndereco;
        if (!$this->containsOnly($bairro, $validos)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarDdd()
    {
        $ddd = $this->registro->getDdd();
        $campo = 'DDD';

        $telefone = $this->registro->getTelefone();
        $outroTelefone = $this->registro->getOutroTelefone();
        // Se foi informado algum telefone e nenhum ddd
        if (($this->isPreenchido($telefone) || $this->isPreenchido($outroTelefone)) && !$this->isPreenchido($ddd)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }
    }

    private function validarTelefones()
    {
        $ddd = $this->registro->getDdd();
        $tel1 = $this->registro->getTelefone();
        $tel2 = $this->registro->getOutroTelefone();

        if ($this->isPreenchido($ddd) && (!$this->isPreenchido($tel1) && !$this->isPreenchido($tel2))) {
            $this->log('Algum telefone deve ser preenchido quando o DDD for preenchido.');
        }
    }

    private function validarTelefone()
    {
        $telefone = $this->registro->getTelefone();
        $campo = 'Telefone';

        if (!$this->isPreenchido($telefone)) {
            return;
        }

        if (strlen($telefone) !== 8 && strlen($telefone) !== 9) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!ctype_digit($telefone)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        // Se a string for a repetição de um só dígito
        if (count(array_unique(str_split($telefone))) <= 1) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (strlen($telefone) === 9 && $telefone[0] !== '9') {
            $this->log(sprintf('O campo "%s" com 9 dígitos deve começar com o caractere numérico 9.', $campo));
        }
    }

    private function validarOutroTelefone()
    {
        $outroTelefone = $this->registro->getOutroTelefone();
        $telefone = $this->registro->getTelefone();
        $campo = 'Outro Telefone De Contato';

        if (!$this->isPreenchido($outroTelefone)) {
            return;
        }

        if (strlen($outroTelefone) !== 8 && strlen($outroTelefone) !== 9) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!ctype_digit($outroTelefone)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        // Se a string for a repetição de um só dígito
        if (count(array_unique(str_split($outroTelefone))) <= 1) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (strlen($outroTelefone) === 9 && $outroTelefone[0] !== '9') {
            $this->log(sprintf('O campo "%s" com 9 dígitos deve começar com o caractere numérico 9.', $campo));
        }

        if ($outroTelefone == $telefone) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEmail()
    {
        $email = $this->registro->getEmail();
        $campo = 'Endereço eletrônico (e-mail) da escola';

        if (!$this->isPreenchido($email)) {
            return;
        }

        if (strlen($email) > 100) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!DBString::isEmailCenso($email)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCodigoOrgaoRegionalEnsino()
    {
        $codigo = $this->registro->getCodigoOrgaoRegionalEnsino();
        $campo = 'Código do órgão regional de ensino';
    }

    private function validarZonaEscola()
    {
        $zona = $this->registro->getZonaEscola();
        $campo = 'Localização/Zona da escola';

        if (!$this->isPreenchido($zona)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($zona, array(1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarLocalizacaoDiferenciada()
    {
        $localizacao = $this->registro->getLocalizacaoDiferenciada();
        $campo = 'Localização diferenciada da escola';

        if (!$this->isPreenchido($localizacao)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($localizacao, array(1, 2, 3, 7, 8))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($localizacao === 1 && $this->registro->getZonaEscola() === 1) {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com 1 (Área de assentamento) quando %s',
                'o campo "Localização/Zona da escola" for preenchido com 1 (urbana).',
                $campo
            ));
        }
    }

    private function validarDependenciaAdministrativa()
    {
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Dependência administrativa';

        if (!$this->isPreenchido($dependencia)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($dependencia, array(1, 2, 3, 4))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if ($dependencia === 3 && $this->registro->getMunicipio() == '5300108') {
            $this->log(sprintf(
                'O campo "%s" não pode ser preenchido com 3 (municipal) quando o campo Município" for preenchido',
                'com 5300108 (Brasília).',
                $campo
            ));
        }
    }

    private function validarOrgaoEscolaPublica()
    {
        $situacao = $this->registro->getSituacaoFuncionamento();

        if (in_array($situacao, array(1, 2, 3))) {
            $preenchido =
                $this->registro->getSecretariaEducacao() === 1 ||
                $this->registro->getSecretariaSeguranca() === 1 ||
                $this->registro->getSecretariaSaude() === 1 ||
                $this->registro->getOutroOrgaoAdministracaoPublica();

            if (!$preenchido) {
                $this->log(sprintf(
                    '%s %s',
                    '"Órgão a que a escola pública está vinculada" Não foi preenchido corretamente.',
                    "Não podem ser informadas todas as opções com valor igual a 0 (Não)."
                ));
            }
        }
    }

    private function validarSecretariaEducacao()
    {
        $secretaria = $this->registro->getSecretariaEducacao();
        $campo = 'Secretaria de Educação/Ministério da Educação';

        if (in_array($this->registro->getDependenciaAdministrativa(), array(1, 2, 3)) &&
            !$this->isPreenchido($secretaria)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($this->registro->getDependenciaAdministrativa(), array(1, 2, 3)) &&
            $this->isPreenchido($secretaria)) {
            $this->log(sprintf('O campo "%s" foi preenchido não quando deveria ser preenchido.', $campo));
        }

        if (!in_array($secretaria, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarSecretariaSeguranca()
    {
        $secretaria = $this->registro->getSecretariaSeguranca();
        $campo = 'Secretaria de Segurança Pública/Forças Armadas/Militar';

        if (in_array($this->registro->getDependenciaAdministrativa(), array(1, 2, 3))
            && !$this->isPreenchido($secretaria)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($this->registro->getDependenciaAdministrativa(), array(1, 2, 3))
            && $this->isPreenchido($secretaria)) {
            $this->log(sprintf('O campo "%s" foi preenchido não quando deveria ser preenchido.', $campo));
        }

        if (!in_array($secretaria, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarSecretariaSaude()
    {
        $secretaria = $this->registro->getSecretariaSaude();
        $campo = 'Secretaria da Saúde/Ministério da Saúde';

        if (in_array($this->registro->getDependenciaAdministrativa(), array(1, 2, 3))
            && !$this->isPreenchido($secretaria)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($this->registro->getDependenciaAdministrativa(), array(1, 2, 3)) &&
            $this->isPreenchido($secretaria)) {
            $this->log(sprintf('O campo "%s" foi preenchido não quando deveria ser preenchido.', $campo));
        }

        if (!in_array($secretaria, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarOutroOrgaoAdministracaoPublica()
    {
        $orgao = $this->registro->getOutroOrgaoAdministracaoPublica();
        $campo = 'Outro órgão da administração pública';

        if (in_array($this->registro->getDependenciaAdministrativa(), array(1, 2, 3)) && !$this->isPreenchido($orgao)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!in_array($this->registro->getDependenciaAdministrativa(), array(1, 2, 3)) && $this->isPreenchido($orgao)) {
            $this->log(sprintf('O campo "%s" foi preenchido não quando deveria ser preenchido.', $campo));
        }

        if (!in_array($orgao, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarMantenedoraEscolaPrivada()
    {
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();

        if ($situacao === 1 && $dependencia === 4) {
            $preenchido =
                $this->registro->getMantenedoraPrivada() === 1 ||
                $this->registro->getMantenedoraSindicatos() === 1 ||
                $this->registro->getMantenedoraOng() === 1 ||
                $this->registro->getMantenedoraInstituicaoSemFimLucrativo() === 1 ||
                $this->registro->getMantenedoraSistemaS() === 1 ||
                $this->registro->getMantenedoraOscip() === 1;

            if (!$preenchido) {
                $this->log('Escola privada sem nenhuma mantenedora.');
            }
        }
    }

    private function validarMantenedoraPrivada()
    {
        $mantenedora = $this->registro->getMantenedoraPrivada();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Empresa, grupos empresariais do setor privado ou pessoa física';

        if ($situacao === 1 && $dependencia === 4 && !$this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($mantenedora)) {
            return;
        }

        if ($situacao != 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia != 4) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($mantenedora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarMantenedoraSindicatos()
    {
        $mantenedora = $this->registro->getMantenedoraSindicatos();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Sindicatos de trabalhadores ou patronais, associações, cooperativas';

        if (!$this->isPreenchido($mantenedora)) {
            return;
        }

        if ($situacao === 1 && $dependencia === 4 && !$this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($situacao != 1 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia != 4 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($mantenedora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarMantenedoraOng()
    {
        $mantenedora = $this->registro->getMantenedoraOng();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Organização não governamental - ONG internacional ou nacional.';

        if (!$this->isPreenchido($mantenedora)) {
            return;
        }

        if ($situacao === 1 && $dependencia === 4 && !$this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($situacao != 1 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia != 4 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($mantenedora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarMantenedoraInstituicaoSemFimLucrativo()
    {
        $mantenedora = $this->registro->getMantenedoraInstituicaoSemFimLucrativo();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Instituição sem fins lucrativos';

        if (!$this->isPreenchido($mantenedora)) {
            return;
        }

        if ($situacao === 1 && $dependencia === 4 && !$this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($situacao != 1 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia != 4 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($mantenedora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarMantenedoraSistemaS()
    {
        $mantenedora = $this->registro->getMantenedoraSistemaS();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Sistema S (Sesi, Senai, Sesc, outros)';

        if (!$this->isPreenchido($mantenedora)) {
            return;
        }

        if ($situacao === 1 && $dependencia === 4 && !$this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($situacao != 1 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia != 4 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($mantenedora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarMantenedoraOscip()
    {
        $mantenedora = $this->registro->getMantenedoraOscip();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Organização da Sociedade Civil de Interesse Público (Oscip)';

        if (!$this->isPreenchido($mantenedora)) {
            return;
        }

        if ($situacao === 1 && $dependencia === 4 && !$this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($situacao != 1 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia != 4 && $this->isPreenchido($mantenedora)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($mantenedora, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCategoriaEscolaPrivada()
    {
        $categoria = $this->registro->getCategoriaEscolaPrivada();
//        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Categoria da escola privada';

        if ($dependencia === 4 && !$this->isPreenchido($categoria)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($categoria)) {
            return;
        }

        if ($dependencia != 4) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($categoria, array(1, 2, 3, 4))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCnpjMantenedoraPrincipal()
    {
        $cnpj = $this->registro->getCnpjMantenedoraPrincipal();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $insituicao = $this->registro->getMantenedoraInstituicaoSemFimLucrativo();
        $regulamentacao = $this->registro->getRegulamentacao();
        $campo = 'CNPJ da mantenedora principal da escola privada';

        // kill($cnpj);

        if ($insituicao === 1 && $regulamentacao === 1 && !$this->isPreenchido($cnpj)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($situacao != 1 && $this->isPreenchido($cnpj)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia != 4 && $this->isPreenchido($cnpj)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($cnpj)) {
            return;
        }

        if (strlen($cnpj) !== 14) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!ctype_digit($cnpj)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (!DBString::isCNPJ($cnpj)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCnpjEscolaPrivada()
    {
        $cnpj = $this->registro->getCnpjEscolaPrivada();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $campo = 'Número do CNPJ da escola privada';

        if ($situacao != 1 && $this->isPreenchido($cnpj)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia != 4 && $this->isPreenchido($cnpj)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($cnpj)) {
            return;
        }

        if (strlen($cnpj) !== 14) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!ctype_digit($cnpj)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (!DBString::isCNPJ($cnpj)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarRegulamentacao()
    {
        $regulamentacao = $this->registro->getRegulamentacao();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $campo = 'Regulamentação/autorização no conselho ou órgão municipal, estadual ou federal de educação';

        if ($situacao == 1 && !$this->isPreenchido($regulamentacao)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }


        if ($situacao != 1 && $this->isPreenchido($regulamentacao)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($situacao, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEsferaAdministrativa()
    {
        $campos = array();
        $campos[] = $this->registro->getEsferaFederal();
        $campos[] = $this->registro->getEsferaEstadual();
        $campos[] = $this->registro->getEsferaMunicipal();

        if (!in_array(1, $campos)) {
            $this->log(
                '"Esfera administrativa do conselho ou órgão responsável pela regulamentação/autorização" ' .
                'não foi preenchido corretamente.' .
                'Não podem ser informadas todas as opções com valor igual a Não.'
            );
        }
    }

    private function validarEsferaFederal()
    {
        $esfera = $this->registro->getEsferaFederal();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $regulamentacao = $this->registro->getRegulamentacao();
        $campo = 'Federal';

        if ($regulamentacao != 0 && !$this->isPreenchido($esfera)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($regulamentacao != 1 && $regulamentacao != 2 && $this->isPreenchido($esfera)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia === 2 && (int) $esfera === 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia === 3 && (int) $esfera === 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($esfera, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEsferaEstadual()
    {
        $esfera = $this->registro->getEsferaEstadual();
        $regulamentacao = $this->registro->getRegulamentacao();
        $campo = 'Estadual';

        if ($regulamentacao != 0 && !$this->isPreenchido($esfera)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($regulamentacao != 1 && $regulamentacao != 2 && $this->isPreenchido($esfera)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($esfera, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarEsferaMunicipal()
    {
        $esfera = $this->registro->getEsferaMunicipal();
        $federal = $this->registro->getEsferaFederal();
        $dependencia = $this->registro->getDependenciaAdministrativa();
        $regulamentacao = $this->registro->getRegulamentacao();
        $municipio = $this->registro->getMunicipio();
        $campo = 'Municipal';

        if ($regulamentacao != 0 && !$this->isPreenchido($esfera)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($regulamentacao != 1 && $regulamentacao != 2 && $this->isPreenchido($esfera)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia === 1 && (int) $esfera === 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($dependencia === 2 && (int) $esfera === 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ($federal === 1 && (int) $esfera === 1) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if ((int) $esfera === 1 && $municipio == '5300108') {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }

        if (!in_array($esfera, array(0, 1))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarUnidadeVinculada()
    {
        $unidade = $this->registro->getUnidadeVinculada();
        $situacao = $this->registro->getSituacaoFuncionamento();
        $campo = 'Unidade vinculada à escola de educação básica ou unidade ofertante de educação superior';

        if ($situacao === 1 && !$this->isPreenchido($unidade)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($situacao != 1 && $this->isPreenchido($unidade)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!in_array($unidade, array(0, 1, 2))) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCodigoEscolaSede()
    {
        $codigo = $this->registro->getCodigoEscolaSede();
        $unidade = $this->registro->getUnidadeVinculada();
        $campo = 'Código da Escola Sede';

        if ($unidade === 1 && !$this->isPreenchido($codigo)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($unidade != 1 && $this->isPreenchido($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido quando não deveria ser preenchido.', $campo));
        }

        if (!$this->isPreenchido($codigo)) {
            return;
        }

        if (strlen($codigo) !== 8) {
            $this->log(sprintf('O campo "%s" está com tamanho diferente do especificado.', $campo));
        }

        if (!ctype_digit($codigo)) {
            $this->log(sprintf('O campo "%s" foi preenchido com valor não permitido.', $campo));
        }
    }

    private function validarCodigoIES()
    {
        $ies = $this->registro->getCodigoIES();
        $unidade = $this->registro->getUnidadeVinculada();
        $campo = 'Código da IES';

        if ($unidade === 2 && !$this->isPreenchido($ies)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }

        if ($unidade != 2 && $this->isPreenchido($ies)) {
            $this->log(sprintf('O campo "%s" não foi preenchido quando deveria ser preenchido.', $campo));
        }
    }

    private function containsOnly($str, $only)
    {
        // Se após a remoção de todos os caracteres válidos a string
        // está vazia, é por que ela não tinha caracteres inválidos
        $res = str_replace($only, '', mb_strtoupper($str));
        return !$this->isPreenchido($res);
    }
}
