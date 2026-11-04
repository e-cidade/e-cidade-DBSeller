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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta_plugin.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("dbforms/db_funcoes.php"));
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1">
<table id="tableFormacao" align="center" border="0" cellspacing="0" cellpading="0" onclick="preview(this,'Documento: Formacao')">
<tr>
<td width="54px" height="38px" style="text-align: center;">
<?php

  if(isset($GLOBALS["_FILES"]["oid_arquivoFormacao"])) {

    db_postmemory($GLOBALS["_FILES"]["oid_arquivoFormacao"]);

    if ( $error == 0 ) {

      $aTipos = array('image/jpeg', 'image/pjpeg', 'image/png');
      if (!in_array($type, $aTipos)) {

        db_msgbox("Imagem em um formato inválido!\\n\\nUtilize somente imagens no formato JPG!");
        ?><script>parent.frame_formacao.document.formFormacao.oid_arquivoFormacao.value = "";</script><?php
      } elseif ($size > 1048576) {

        db_msgbox("Tamanho da imagem  maior que o permitido!\\n\\nUtilize imagens at 1 MB!");
        ?><script>parent.frame_formacao.document.formFormacao.oid_arquivoFormacao.value = "";</script><?php
      } else {

        // Pega o tamanho da imagem e proporo de resize
        $img_size = getimagesize($tmp_name);
        $scale = @min(720/$img_size[0], 1280/$img_size[1]);
        $sExtencao = ".jpg";
        if ( $type == 'image/png') {
          $sExtencao = ".png";
        }

        $imagem_gerada = "tmp/formacao".time()."$sExtencao";
        $caminhoCorreto = $imagem_gerada;
        // Se a imagem no est no tmp/ ela  criada
        if (!file_exists($imagem_gerada)) {
          // Se a imagem  maior que o permitido(200x200), encolhe ela
          if ($scale < 1) {

            $new_width  = floor($scale * $img_size[0]);
            $new_height = floor($scale * $img_size[1]);
          } else {//seno fica o mesmo tamanho
            $new_width = $img_size[0];
            $new_height = $img_size[1];
          }

          //cria uma nova imagem com o novo tamanho
          $img_new = imagecreatetruecolor($new_width, $new_height);
          switch ($type){

            case 'image/jpeg':
            case 'image/pjpeg': // jpg
              $origem = imagecreatefromjpeg($tmp_name);
              imagecopyresampled($img_new, $origem, 0, 0, 0, 0, $new_width, $new_height, $img_size[0], $img_size[1]);
              imagejpeg($img_new, $imagem_gerada);
              break;
            case 'image/png': // png
              $origem = imagecreatefrompng($tmp_name);
              imagecopyresampled($img_new, $origem, 0, 0, 0, 0, $new_width, $new_height, $img_size[0], $img_size[1]);
              imagepng($img_new, $imagem_gerada);
              break;
          }
          imagedestroy($origem);
          imagedestroy($img_new);
        }
        //retira o 'tmp/' do nome da imagem para gravar no bd
        $parentname = str_replace("tmp/","",$imagem_gerada);
        echo "<center style='width:54px; height:38px;'>";
        ?>
        <img style="width:100%; height:100%;" src="<?php echo $imagem_gerada?>">
        <?php

        if( empty( $scale ) ){

          echo "<p>Visualizao no disponvel";
          $parentname = "";
        }
        echo $parentname;
        ?>
         <script>
          parent.document.form1.oid_arquivoFormacao.value = "<?php echo $caminhoCorreto?>";
         </script>
        <?php
        echo "</center>";
      }
    } else {
      ?>
        <script>
          alert("Erro na importao da imagem");
        </script>
      <?php
    }
  }
  if(isset($_GET["imagem_gerada"]) && !empty($_GET["imagem_gerada"])){
    $caminhoCorreto = ECIDADE_REQUEST_PATH . 'tmp/'. $_GET["imagem_gerada"];
    ?>
    <img width="100%" height="100%" src="<?php echo $caminhoCorreto?>"
      data-arquivo="<?php echo 'tmp/'. $_GET["imagem_gerada"]?>">
  <?php
  } else { ?>
    <i class="fa fa-times" style="font-size: 40px;color: #a1a0a0;">
 <?php
  } ?>
 </td>
</tr>
</table>
<script type="text/javascript">
  function preview(e, doc){
    if(!!parent.document.body.querySelector('#preview')) {
      parent.document.body.querySelector('#preview').remove();
    };

    let img = document.createElement('img');
    let src = e.querySelector('img').getAttribute('src');
    let arquivo = e.querySelector('img').dataset.arquivo;
    img.setAttribute('src', src);
    img.setAttribute('style', "border-radius:12px;");

    let btn = document.createElement('div');
    btn.setAttribute('style', 'position:absolute;left:-4px;top:-28px;background:#4a789c;width:100%;height:20px;display:flex;border:3px solid #bbb;box-shadow: 2px 3px #333;');

    let txt = document.createElement('p');
    txt.setAttribute('style', 'margin:4px;color:#fff;font-weight:bold;');
    txt.innerHTML = doc;
    btn.appendChild(txt);

    let ctIcon = document.createElement('div');
    ctIcon.setAttribute('style','position:absolute;background:#004b6b;right:5px;top:2px;width:16px;height:16px;border-radius:100%;cursor:pointer;');

    let icon = document.createElement('i');
    icon.classList.add('fa');
    icon.classList.add('fa-times');
    icon.setAttribute('style','color:#4a789c;position:absolute;left:4px;top:1px;cursor:pointer;');
    ctIcon.appendChild(icon);
    btn.appendChild(ctIcon);

    let download = document.createElement('p');
    download.innerHTML = 'Download';
    download.setAttribute('style', 'position: absolute;bottom:0;width:94%;text-align:center;left:3%;margin: 5px 0;font-weight: bold;background: #4a789c;color: #fff;box-sizing: border-box;cursor: pointer;padding: 2px;');
    download.addEventListener("click", function(e){
      e.stopPropagation();
      window.open("db_download.php?arquivo="+arquivo);
    });

    let box = document.createElement('div');
    box.setAttribute('style', "position:absolute;top:5%;left:50%;margin-left:-"+(img.naturalWidth/2+5)+"px;border-radius:3px;padding:10px 10px 30px;background:#fff;border:4px solid #bbb;box-shadow:inset 2px 0px #333;");
    box.appendChild(btn);
    box.appendChild(download);

    let ct = document.createElement('div');
    ct.setAttribute('style', "position:absolute;top:0;left:0;width:100%;height:100%;");
    ct.setAttribute('id', 'preview');
    ct.addEventListener("click", function(){ct.remove()}, false);
    ct.appendChild(box);
    ct.appendChild(box);
    box.appendChild(img);
    parent.document.body.appendChild(ct);
  }
</script>
</body>
</html>
