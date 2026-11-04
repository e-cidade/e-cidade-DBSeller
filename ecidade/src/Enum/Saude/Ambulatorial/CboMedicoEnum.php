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

namespace ECidade\Enum\Saude\Ambulatorial;

use ECidade\Enum\Enum;

class CboMedicoEnum extends Enum
{
    const MEDICO_INFECTOLOGISTA = 225103;
    const MEDICO_ACUPUNTURISTA = 225105;
    const MEDICO_LEGISTA = 225106;
    const MEDICO_NEFROLOGISTA = 225109;
    const MEDICO_ALERGISTA_E_IMUNOLOGISTA = 225110;
    const MEDICO_NEUROLOGISTA = 225112;
    const MEDICO_ANGIOLOGISTA = 225115;
    const MEDICO_NUTROLOGISTA = 225118;
    const MEDICO_CARDIOLOGISTA = 225120;
    const MEDICO_ONCOLOGISTA_CLINICO = 225121;
    const MEDICO_CANCEROLOGISTA_PEDIATRICO = 225122;
    const MEDICO_PEDIATRA = 225124;
    const MEDICO_CLINICO = 225125;
    const MEDICO_PNEUMOLOGISTA = 225127;
    const MEDICO_DE_FAMILIA_E_COMUNIDADE = 225130;
    const MEDICO_PSIQUIATRA = 225133;
    const MEDICO_DERMATOLOGISTA = 225135;
    const MEDICO_REUMATOLOGISTA = 225136;
    const MEDICO_SANITARISTA = 225139;
    const MEDICO_DO_TRABALHO = 225140;
    const MEDICO_DA_ESTRATEGIA_DE_SAUDE_DA_FAMILIA = 225142;
    const MEDICO_EM_MEDICINA_DE_TRAFEGO = 225145;
    const MEDICO_ANATOMOPATOLOGISTA = 225148;
    const MEDICO_EM_MEDICINA_INTENSIVA = 225150;
    const MEDICO_ANESTESIOLOGISTA = 225151;
    const MEDICO_ENDOCRINOLOGISTA_E_METABOLOGISTA = 225155;
    const MEDICO_FISIATRA = 225160;
    const MEDICO_GASTROENTEROLOGISTA = 225165;
    const MEDICO_GENERALISTA = 225170;
    const MEDICO_GENETICISTA = 225175;
    const MEDICO_GERIATRA = 225180;
    const MEDICO_HEMATOLOGISTA = 225185;
    const MEDICO_HOMEOPATA = 225195;
    const MEDICO_EM_CIRURGIA_VASCULAR = 225203;
    const MEDICO_CIRURGIAO_CARDIOVASCULAR = 225210;
    const MEDICO_CIRURGIAO_DE_CABECA_E_PESCOCO = 225215;
    const MEDICO_CIRURGIAO_DO_APARELHO_DIGESTIVO = 225220;
    const MEDICO_CIRURGIAO_GERAL = 225225;
    const MEDICO_CIRURGIAO_PEDIATRICO = 225230;
    const MEDICO_CIRURGIAO_PLASTICO = 225235;
    const MEDICO_CIRURGIAO_TORACICO = 225240;
    const MEDICO_GINECOLOGISTA_E_OBSTETRA = 225250;
    const MEDICO_MASTOLOGISTA = 225255;
    const MEDICO_NEUROCIRURGIAO = 225260;
    const MEDICO_OFTALMOLOGISTA = 225265;
    const MEDICO_ORTOPEDISTA_E_TRAUMATOLOGISTA = 225270;
    const MEDICO_OTORRINOLARINGOLOGISTA = 225275;
    const MEDICO_PROCTOLOGISTA = 225280;
    const MEDICO_UROLOGISTA = 225285;
    const MEDICO_CANCEROLOGISTA_CIRURGICO = 225290;
    const MEDICO_CIRURGIAO_DA_MAO = 225295;
    const MEDICO_CITOPATOLOGISTA = 225305;
    const MEDICO_EM_ENDOSCOPIA = 225310;
    const MEDICO_EM_MEDICINA_NUCLEAR = 225315;
    const MEDICO_EM_RADIOLOGIA_E_DIAGNOSTICO_POR_IMAGEM = 225320;
    const MEDICO_PATOLOGISTA = 225325;
    const MEDICO_RADIOTERAPEUTA = 225330;
    const MEDICO_PATOLOGISTA_CLINICO_MEDICINA_LABORATORIAL = 225335;
    const MEDICO_HEMOTERAPEUTA = 225340;
    const MEDICO_HIPERBARISTA = 225345;
    const MEDICO_NEUROFISIOLOGISTA = 225350;

    /**
     * Funчуo que valida se o cbo informado щ pertencente a um profissional mщdico.
     * @param $cbo
     * @return bool
     */
    public static function validaCBOMedico($cbo)
    {
        return in_array($cbo, CboMedicoEnum::toArray());
    }
}
