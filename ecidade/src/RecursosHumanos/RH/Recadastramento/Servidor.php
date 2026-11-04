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

namespace ECidade\RecursosHumanos\RH\Recadastramento;

use AtendimentoOuvidoria;
use ECidade\RecursosHumanos\RH\Recadastramento\conversorJson\Formatter;

abstract class Servidor
{
    protected $form;
    protected $matricula;
    protected $servidor;
    protected $cgm;
    protected $atendimentoOvidoria;

    /**
     * @throws \Exception
     */
    public function __construct($matricula, Formatter $form, AtendimentoOuvidoria $atendimento, $instituicaoMatricula)
    {
        $this->matricula = $matricula;
        $this->form = $form;
        $this->servidor = new \Servidor($this->matricula, null, null, $instituicaoMatricula);
        $this->cgm = $this->servidor->getCgm();
        $this->atendimentoOvidoria = $atendimento;

        try {
            $this->run();
        } catch (\Exception $ex) {
            throw  new \Exception("Ocorreu um erro ao processar matricula {$matricula}  : {$ex->getMessage()}");
        }
    }

    abstract public function run();

    protected function racaParaEcidade($codigoRecadastramento)
    {

        $raca = null;
        switch ((int)$codigoRecadastramento) :
            case 1:
                $raca = 2; //Branca
                break;
            case 2:
                $raca = 4;//PRETA
                break;
            case 3:
                $raca = 8;//PARDA
                break;
            case 4:
                $raca = 6;//AMARELA
                break;
            case 5:
                $raca = 1;//Indígena
                break;
            case 6:
                $raca = 9;//Não Informado
                break;
        endswitch;

        return $raca;
    }

    protected function apenasNumero($valor)
    {
        return preg_replace('/[^0-9]/', '', trim($valor));
    }

    /**
     * @param $parentescoEsocial
     * @param $sexo
     */
    protected function parentescoParaEcidade($parentescoEsocial, $sexo)
    {

        $parentescoVaue = "F";
        switch ((int)$parentescoEsocial) {
            /**
             * Cônjuge
             */
            case 1:
                /**
                 *Companheiro(a) com o(a) qual tenha filho
                 * ou viva há mais de 5(cinco)
                 * anos ou possua declaração de união estável
                 */
            case 2:
                $parentescoVaue = "C";
                break;
            case 3:  //Filho(a) ou enteado(a)
            case 4: //Filho(a) ou enteado(a), universitário(a) ou cursando escola técnica de 2º grau
                $parentescoVaue = "F";
                break;
            case 6: //Irmão(ã), neto(a) ou bisneto(a) sem arrimo dos pais, do(a) qual detenha a guarda judicial
                /**
                 * Irmão(ã), neto(a) ou bisneto(a) sem arrimo dos pais, universitário(a)
                 * ou cursando escola técnica de 2° grau, do(a) qual detenha a guarda judicial
                 */
            case 7:
                $parentescoVaue = "O";
                break;
            case 9: //Pais, avós e bisavós
                if ($sexo == "M") {
                    $parentescoVaue = "P";
                } else {
                    $parentescoVaue = "M";
                }
                break;
            case 10: //Menor pobre do qual detenha a guarda judicial
            case 11: //A pessoa absolutamente incapaz, da qual seja tutor ou curador
            case 12: //Ex-cônjuge
            case 99: //Agregado/Outros
                $parentescoVaue = "O";
                break;
        }

        return $parentescoVaue;
    }

    protected function salarioFamiliaParaEcidade($codigo, $parentesco)
    {
        if (intval($codigo) == 1) {
            //FILHO
            if ($parentesco == "F") {
                return "C";
            }
            //CONJUGE
            if ($parentesco == "C") {
                return "S";
            }
        }
        return "N";
    }

    protected function irfParaEcidade($codigo, $parentescoEsocial)
    {
        $irf = 0;
        if ((int) $codigo == 1) {
            switch ((int)$parentescoEsocial) {
                /**
                 * Cônjuge
                 */
                case 1:
                    /**
                     *Companheiro(a) com o(a) qual tenha filho
                     * ou viva há mais de 5(cinco)
                     * anos ou possua declaração de união estável
                     */
                case 2:
                    $irf = 1;
                    break;
                case 3:
                    $irf = 2;
                    break;
                case 6:
                    $irf = 3;
                    break;
                case 9:
                    $irf = 4;
                    break;
                case 11:
                    $irf = 5;
                    break;
                case 4:
                    $irf = 6;
                    break;
                case 7:
                    $irf = 7;
                    break;
            }
        }
        return $irf;
    }

    protected function estadoCivilParaEcidade($codigo)
    {

        $estadoCivil = 0;
        switch ((int)$codigo) {
            case 1: //Solteiro
                $estadoCivil = 1;
                break;
            case 2: //Casado
                $estadoCivil = 2;
                break;
            case 3: //Divorciado
                $estadoCivil = 4;
                break;
            case 4: //Separado
                $estadoCivil = 5;
                break;
            case 5: //Viúvo
                $estadoCivil = 3;
                break;
        }

        return $estadoCivil;
    }

    protected function estadoCivilParaEcidadeServidor($codigo)
    {

        $estadoCivil = 6;
        switch ((int)$codigo) {
            case 1: //Solteiro
                $estadoCivil = 1;
                break;
            case 2: //Casado
                $estadoCivil = 2;
                break;
            case 3: //Divorciado
                $estadoCivil = 5;
                break;
            case 4: //Separado
                $estadoCivil = 4;
                break;
            case 5: //Viúvo
                $estadoCivil = 3;
                break;
        }

        return $estadoCivil;
    }

    protected function tipoPrevidencia($codigo)
    {

        $tipo = 0;
        switch ((int)$codigo) {
            case 1:
                $tipo = 2;
                break;
            case 2:
                $tipo = 3;
                break;
        }

        return $tipo;
    }

    protected function limpaEndereco()
    {
        $this->cgm->setEnderecoPrimario('');
    }
}
