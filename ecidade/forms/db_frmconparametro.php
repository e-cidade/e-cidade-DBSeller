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

//MODULO: contabilidade
require_once(modification("libs/db_conecta.php"));
$clconparametro->rotulo->label();


$sNomeArquivo  = "config/utiliza_domicilio_bancario.txt";
$sLogin  = db_getsession('DB_login');
$sData   = date('d/m/Y H:i:s',db_getsession('DB_datausu'));
$sIP     = db_getsession('DB_ip');
$disable = "";
$sTextoAtivado = "";

if (file_exists($sNomeArquivo)){
    $sTextoAtivado = file_get_contents($sNomeArquivo);
    $disable = "disabled";
}

if ( isset($_POST["btnAtivarDomicilioBancario"]) ) {

    $sTextoAtivado = "Ativado pelo usuário <b>{$sLogin}</b> dia <b>{$sData}</b>. Endereço de IP: <b>{$sIP}</b>";
    file_put_contents($sNomeArquivo, $sTextoAtivado);
    $disable = "disabled";
}
?>

<div class="container">
    <form name="form1" method="post" action="">
        <fieldset>
            <legend>
                <b>Parâmetros da Contabilidade</b>
            </legend>
            <table border="0">
                <tr>
                    <td nowrap title="<?=@$Tc90_estrutsistema?>">
                        <input name="oid" type="hidden" value="<?=@$oid?>">
                        <?=@$Lc90_estrutsistema?>
                    </td>
                    <td>
                        <?php
                        db_input('c90_estrutsistema',50,$Ic90_estrutsistema,true,'text',$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Tc90_estrutcontabil?>">
                        <?=@$Lc90_estrutcontabil?>
                    </td>
                    <td>
                        <?php
                        db_input('c90_estrutcontabil',50,$Ic90_estrutcontabil,true,'text',$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Tc90_codestrut?>">
                        <?=@$Lc90_codestrut?>
                    </td>
                    <td>
                        <?php
                        db_input('c90_codestrut',8,$Ic90_codestrut,true,'text',$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Tc90_utilcontabancaria?>">
                        <?=@$Lc90_utilcontabancaria?>
                    </td>
                    <td>
                        <?php
                        $aUtilContaBancaria = array( 'f'=>'Não',
                            't'=>'Sim');

                        db_select('c90_utilcontabancaria',$aUtilContaBancaria,true,1,"style='width:80px;'");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?=@$Tc90_confirmadata?>">
                        <?=@$Lc90_confirmadata?>
                    </td>
                    <td>
                        <?
                        $aConfirmaData = array( 'f'=>'Não','t'=>'Sim');

                        db_select('c90_confirmadata',$aConfirmaData,true,1,"style='width:80px;'");
                        ?>
                    </td>

                </tr>
                <tr>
                    <td nowrap title="<?=@$Tc90_usapcasp?>">
                        <?=@$Lc90_usapcasp?>
                    </td>
                    <td>
                        <?php
                        $aUsaPCASP = array( 'f'=>'Não',
                            't'=>'Sim');

                        db_select('c90_usapcasp', $aUsaPCASP, true, 1, "style='width:80px;'");
                        ?>
                    </td>
                </tr>

                <tr>
                    <td class="bold">Utiliza Domicílio Bancário:</td>
                    <td>
                        <input type="submit" value="<?php echo empty($disable) ? 'Ativar' : 'Ativo';?>" name="btnAtivarDomicilioBancario" id="btnAtivarDomicilioBancario" onclick="return ativarDomicilioBancario();" <?php echo $disable; ?>/>&nbsp;
                        <?php echo $sTextoAtivado; ?>
                    </td>
                </tr>
                <tr>
                    <td class="bold">Modelo Anexo 3 RREO:</td>
                    <td>
                        <?php
                        $opcaoRelatorio = \ECidade\Configuracao\Opcao\Opcao::get(
                            'modelo_rreo_anexo3',
                            db_getsession("DB_anousu")
                        );
                        $modelo_rreo_anexo3 = empty($opcaoRelatorio) ? 'mdf' : $opcaoRelatorio;
                        $modelos = array(
                            'in13' =>'Modelo In13',
                            'in13Ro' =>'Modelo Porto Velho',
                            'mdf' =>'Modelo MDF')
                        ;

                        db_select('modelo_rreo_anexo3', $modelos, true, 1, "style='width:200px;'");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="bold">Modelo Anexo 1 RGF:</td>
                    <td>
                        <?php
                        $opcaoRelatorio = \ECidade\Configuracao\Opcao\Opcao::get(
                            'modelo_anexo_1_rgf',
                            db_getsession("DB_anousu")
                        );
                        $modelo_anexo_1_rgf = empty($opcaoRelatorio) ? 'mdf' : $opcaoRelatorio;
                        $modelos = array(
                            'mdf' =>'Modelo MDF',
                            'in13'=>'Modelo IN13',
                            'rondonia'=>'Modelo Rondônia',
                        );

                        db_select('modelo_anexo_1_rgf', $modelos, true, 1, "style='width:200px;'");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="bold">% Limite ASPS Lei Orgânica Ente:</td>
                    <td>
                        <?php
                        $limite_asps_lei_organica = \ECidade\Configuracao\Opcao\Opcao::get(
                            'limite_asps_lei_organica',
                            db_getsession("DB_anousu")
                        );
                        db_input('limite_asps_lei_organica', 10, 4,true,'text',$db_opcao,"");
                        ?>
                    </td>
                </tr>
            </table>
        </fieldset>
        <input name="db_opcao" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
    </form>
</div>

<script>

    function ativarDomicilioBancario()
    {
        var mensagem = "Tem certeza que deseja ativar o Domicílio Bancário? Este procedimento é irreversível.";
        if (!confirm(mensagem)) {
            return false;
        }
        return true;
    }
</script>
