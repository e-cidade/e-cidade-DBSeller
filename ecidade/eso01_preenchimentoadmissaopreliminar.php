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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');

db_postmemory($_POST);

?>
<html>
<head>
    <title>DBSeller Informática Ltda</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("scripts.js");
    db_app::load("prototype.js");
    db_app::load("object.js");
    db_app::load("widgets/DBLookUp.widget.js");
    db_app::load("Input/DBInput.widget.js, DBInputHora.widget.js, Input/DBInputCep.widget.js,Input/DBInputCNPJ.js,Input/DBInputCpf.widget.js,Input/DBInputDate.widget.js");
    db_app::load("Input/DBInputInteger.widget.js, Input/DBInputTelefone.widget.js,Input/DBInputValor.widget.js");
    db_app::load("Input/DBInputCheckboxRadio.widget.js, Input/DBCheckBox.widget.js,Input/DBRadio.widget.js,Collection.widget.js");
    db_app::load("avaliacao/DBViewFormulario.classe.js, avaliacao/DBViewGrupoPerguntas.classe.js,avaliacao/DBViewPergunta.classe.js,avaliacao/DBViewResposta.classe.js");
    db_app::load("AjaxRequest.js,estilos.css,grid.style.css,avaliacao.css");
    
    ?>
    <!-- ToDo Avaliar a possibilidade de padronizar com app::load, por hora não esta funcionando. -->
    <link rel="stylesheet" href="estilos/awesomplete.css">
    <script src="scripts/awesomplete.js"></script>
    <script src="scripts/classes/avaliacao/DBAutoComplete.js"></script>
    <script src="scripts/classes/eSocial/DBAutoCompleteEsocial.js"></script>
    <style>
        .controle {
            width: 80px;
        }

        .db-tooltip {
            display: none;
        }
    </style>
</head>
<body onload="$('matricula').click()" >
<form class="container" style="width: 800px;">
    <fieldset>
        <legend><label for="cgm">Escolha o Empregador</label></legend>
        <select id='cgm' style="width:100%" onchange='buscarAvaliacao();'>
            <option value="">Selecione o empregador</option>
        </select>
    </fieldset>
    <fieldset>
        <legend>Conferência dos dados informados pelo servidor:</legend>
        <table class="form-container">
          <tr>
            <td nowrap title="matricula">
              <a for="matricula">Matricula:</a>
            </td>
            <td>
              <?php db_input('matricula', 10, "", true, "text", 3); ?>
            </td>
          </tr>
          <tr>
            <td nowrap title="<?php echo ""; ?>">
              <label for="z01_nome">Servidor:</label>
            </td>
            <td><?php db_input('nome', 50, "", true, "text", 3); ?></td>
          </tr>
        </table>
      </fieldset>
    <fieldset>
        <legend>Formulário de Cadastro para o eSocial</legend>
        <div id="questionario"></div>
    </fieldset>
    <input type="button" id="novo" name="novo" value="Novo" class="controle" onclick="js_editapreenchimento()"/>
    <input type="button" id="salvar" name="salvar" value="Salvar" class="controle" disabled/>
    <input type="button" id="pesquisar" name="pesquisar" value="Pesquisar" />
    <input type="hidden" id="servidorSemRegistro" name="servidorSemRegistro" value="false" />
</form>
<script type="text/javascript">

  var viewAvaliacao = '';
  var iCGMAnterior = '';
  var sRpc = 'eso04_preenchimentoadmissaopreliminar.RPC.php';
  var instituicao = '';
  var inputCpfTrabalhador = '';
  (function() {
    instituicao = <?=db_getsession("DB_instit")?>;
    var parametros = {'exec': 'getEmpregadores', 'instituicao': instituicao};

    new AjaxRequest('eso4_esocialapi.RPC.php', parametros, function(retorno) {
      if (retorno.erro) {
        alert('Desculpe, não encontramos nenhum Empregador vinculado na instituição.\nContate o suporte.');
        return;
      }

      $('cgm').options.length = 0;
      $('cgm').add(new Option('Selecione o empregador', ''));
      for (var empregador of retorno.empregadores) {
        var nome = empregador.documento + ' - ' + empregador.nome;
        $('cgm').add(new Option(nome, empregador.cgm));
      }

      if (retorno.empregadores.length == 1) {
        $('cgm').value = retorno.empregadores[0].cgm;
      }
    }).setMessage('Buscando empregadores.').execute();
  })();

  function buscarAvaliacao() {

    if ($F('cgm') == '') {
      $('salvar').disabled = true;
      $('novo').disabled = true;
      $('questionario').innerHTML = '';
      return false;
    }

    if (!empty(iCGMAnterior) && iCGMAnterior != $F('cgm')) {
      if (!confirmaSaida(
              'Se você trocar de empregador os dados que não foram salvos serão perdidos.\nTem certeza que deseja trocar?')) {
        $('cgm').value = iCGMAnterior;
        return false;
      }
    }

    iCGMAnterior = $F('cgm');
    removeEventoBotoes();
    $('salvar').disabled = false;
    $('novo').disabled = false;
    $('questionario').innerHTML = '';

    var oDados = {
      'exec': 'buscarAvaliacao',
      'iCGM': $F('cgm'),
      'matricula': $('matricula').value,
      'servidorSemRegistro': $('servidorSemRegistro').value,
    };

    const buscarAvaliacaoRequest = AjaxRequest.create(sRpc, oDados, montarAvaliacao).setMessage('Buscando dados...');
    buscarAvaliacaoRequest.lAsynchronous = false;
    buscarAvaliacaoRequest.execute();
  }

  function montarAvaliacao(oResponse, lErro) {

    if (lErro) {
      alert(oResponse.mensagem);
    }

    viewAvaliacao = DBViewFormulario.makeFromObject(oResponse.oFormulario).
        setEvent('changeStep', controlarBotoes).
        show($('questionario'));

     inputCpfTrabalhador = document.querySelector("input[identificador=cpfTrab]");

    $('salvar').observe('click', function() {
      salvarQuestionario(viewAvaliacao);
    });
  }

  $('novo').observe('click', function() {
    novoFormulario();
  });

  function removeEventoBotoes() {
    $('salvar').stopObserving('click');
  }

  function confirmaSaida(sMensagem) {
    if (typeof sMensagem == 'undefined' || sMensagem == null || sMensagem == false) {
      sMensagem = 'Você está saindo do cadastro do e-social.\nAntes de sair, salve seus dados.';
    }

    if (!confirm(sMensagem)) {
      return false;
    }
    return true;
  }

  var controlarBotoes = function(event) {
    DBAutoCompleteEsocial.gerarAutoComplete();
    var status = this.getStatus();
    $('salvar').disabled = true;
    $('novo').disabled = true;

    if (status.grupoAtual) {
      $('salvar').disabled = false;
      $('novo').disabled = false;
    }
  };

  function salvarQuestionario(viewAvaliacao) {

    if (!viewAvaliacao.getStatus().grupoAtual.isValido()) {
      alert('Há informações obrigatórias inconsistentes.\nVerifique.');
      return false;
    }

    if (inputCpfTrabalhador.value === "") {
        alert("Campo CPF do trabalhador é obrigatório.");
    }

    AjaxRequest.create(
        sRpc,
        {
          exec: 'salvarAvaliacao',
          iCGM: iCGMAnterior,
          iCodigoAvaliacao: viewAvaliacao.codigo,
          aPerguntasRespostas: viewAvaliacao.getDados(),
          cpfTrabalhador: inputCpfTrabalhador.value,
          matricula: $('matricula').value,
        },
        function(oResponse, lErro) {
            alert(oResponse.mensagem);

          if (lErro) {
            return false;
          }
        },
    ).setMessage('Salvando dados...').execute();
    return true;
  }

  function novoFormulario() {
    if ($F('cgm') != '' && $('matricula').value) {
      if (confirm(
              'Se você criar um Processo Administrativo/Judicial novo, os dados que não foram salvos serão perdidos.\nTem certeza que deseja continuar?')) {
        buscarAvaliacao();

      } else {
        return false;
      }
    } else {
      buscarAvaliacao();
    }
  }

  function js_editapreenchimento() {
    if (!$('matricula').value) {
      $('servidorSemRegistro').value = true;
    }
  }

</script>

<script>

const selectEmpregador = document.getElementById('empregador');
const inputPesquisar = document.getElementById('pesquisar');
const inputMatricula = document.getElementById('matricula');
const inputNome = document.getElementById('nome');

const dbLookUp = new DBLookUp(
    document.createElement('a'),
    document.createElement('input'),
    document.createElement('input'),
    {
        'sArquivo': 'func_rhpessoaladmiss.php',
        'oObjetoLookUp': 'func_nome',
        'aParametrosAdicionais': [
            'vinculados=true'
        ]
    }
);

dbLookUp.setCamposAdicionais(['rh01_regist','z01_nome', 'z01_cgccpf', 'rh01_admiss', 'z01_nasc']);
dbLookUp.setCallBack('onClick', argumentos => {
    inputMatricula.value = argumentos[0];
    inputNome.value = argumentos[3];
    
    buscarAvaliacao();
    document.querySelector("input[identificador=matricula]").value = argumentos[0];
    inputCpfTrabalhador.value = argumentos[4].replace(/(\d{3})?(\d{3})?(\d{3})?(\d{2})/, "$1.$2.$3-$4");
    document.querySelector("input[identificador=dtAdm]").value = argumentos[5].split('-').reverse().join('/');
    document.querySelector("input[identificador=dtNascto]").value = argumentos[6].split('-').reverse().join('/');
});

const pesquisar = () => {
    dbLookUp.abrirJanela(true);
};

inputPesquisar.addEventListener('click', pesquisar);
</script>
<?php db_menu(); ?>
</body>
