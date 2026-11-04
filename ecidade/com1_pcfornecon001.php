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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_pcfornecon_classe.php"));
require_once(modification("classes/db_pcforneconpad_classe.php"));
require_once(modification("classes/db_pcforne_classe.php"));
require_once(modification("classes/db_empagemovconta_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_POST);
$clpcfornecon = new cl_pcfornecon;
$clpcforneconpad = new cl_pcforneconpad;
$clpcforne = new cl_pcforne;
$clempagemovconta = new cl_empagemovconta;
$db_opcao = 22;
$db_botao = false;

if (isset($alterar) || isset($excluir) || isset($incluir)) {
    $sqlerro = false;
}

if (isset($novo) && isset($z01_numcgm)) {
    $result = $clpcforne->sql_record($clpcforne->sql_query_file($z01_numcgm));

    if ($clpcforne->numrows > 0) {
        $pc63_numcgm = $z01_numcgm;
    } else {
        $clpcforne->pc60_dtlanc = date("Y-m-d", db_getsession("DB_datausu"));
        $clpcforne->pc60_bloqueado = 'false';
        $clpcforne->pc60_indicativocprb = 'false';
        $clpcforne->pc60_numcgm = $z01_numcgm;
        $clpcforne->pc60_hora = db_hora();
        $clpcforne->pc60_usuario = db_getsession("DB_id_usuario");
        $clpcforne->incluir($z01_numcgm);

        if ($clpcforne->erro_status == 0) {
            $clpcforne->erro_msg;
            db_msgbox("Erro ao cadastrar cgm como fornecedor... Contate suporte.\\n\\n" . $clpcforne->erro_msg);
        } else {
            $pc63_numcgm = $z01_numcgm;
        }
    }
}

function inputPixValido($tipo, $chave)
{
    if ($tipo > 1) {
        if (($tipo == 2 && strlen(preg_replace('/[^0-9]+/', '', $chave)) == 0) ||
        ($tipo == 3 && strlen(preg_replace('/[^0-9]+/', '', $chave)) == 0) ||
        ($tipo == 4 && strlen(preg_replace('/[^0-9]+/', '', $chave)) == 0) ||
        ($tipo == 5 && strlen($chave) == 0) ||
        ($tipo == 6 && strlen($chave) == 0)
        ) {
            return false;
        }
    }

    return true;
}

function tratarChavePix($tipo, $chave)
{
    if ($tipo > 1) {
        if (in_array($tipo, [2, 3, 4])) {
            return preg_replace('/[^0-9]+/', '', $chave);
        }
        return $chave;
    }
    return $chave;
}

if (isset($incluir)) {
    if (!$sqlerro) {
        db_inicio_transacao();
        $pc63_contabanco = 0;

        if (isset($pc63_tipopix) && isset($pc63_chavepix)) {
            if (!inputPixValido($pc63_tipopix, $pc63_chavepix)) {
                $sqlerro = true;
                $erro_msg = "Usuário:\\n\\nDigite uma chave PIX.\\n\\nAdministrador:";
            }
        }


        if (strlen($pc63_banco) > 3) {
            $sqlerro = true;
            $erro_msg = "Usuário:\\n\\nBanco deve ter no máximo três(3) caracteres.\\n\\nAdministrador:";
        } elseif (strlen($pc63_agencia) > 5) {
            $sqlerro = true;
            $erro_msg = "Usuário:\\n\\nAgência deve ter no máximo cinco(5) caracteres.\\n\\nAdministrador:";
        } elseif (strlen($pc63_conta) > 12) {
            $sqlerro = true;
            $erro_msg = "Usuário:\\n\\nConta deve ter no máximo doze(12) caracteres.\\n\\nAdministrador.";
        }
        if (!$sqlerro) {
            if (isset($conferido)) {
                $clpcfornecon->pc63_dataconf = date("Y-m-d", db_getsession("DB_datausu"));
            }
            $clpcfornecon->pc63_codigooperacao = str_pad(@$pc63_codigooperacao, 4, "0", STR_PAD_LEFT);

            if (isset($pc63_tipopix) && isset($pc63_chavepix)) {
                $clpcfornecon->pc63_chavepix = tratarChavePix($pc63_tipopix, $pc63_chavepix);
            }
            $clpcfornecon->incluir($pc63_contabanco);

            if ($clpcfornecon->erro_status == 0) {
                $erro_msg = $clpcfornecon->erro_msg;
                $sqlerro = true;
            } else {
                $erro_msg = $clpcfornecon->erro_msg;

                if ($pc64_contabanco == 't') {
                    $result = $clpcfornecon->sql_record($clpcfornecon->sql_query(null, "pc63_contabanco as contabco", "pc63_contabanco", "pc63_numcgm = $pc63_numcgm"));

                    if ($result != 0 && $clpcfornecon->numrows) {
                        for ($i = 0; $i < $clpcfornecon->numrows; $i++) {
                            db_fieldsmemory($result, $i);
                            $clpcforneconpad->excluir($contabco);
                        }
                    }

                    $clpcforneconpad->pc64_contabanco = $clpcfornecon->pc63_contabanco;
                    $clpcforneconpad->incluir($clpcfornecon->pc63_contabanco);

                    if ($clpcforneconpad->erro_status == 0) {
                        $erro_msg = $clpcforneconpad->erro_msg;
                        $sqlerro = true;
                    }
                }
            }
        }
        db_fim_transacao($sqlerro);
    }
} elseif (isset($alterar)) {
    if (isset($pc63_tipopix) && isset($pc63_chavepix)) {
        if (!inputPixValido($pc63_tipopix, $pc63_chavepix)) {
            $sqlerro = true;
            $erro_msg = "Usuário:\\n\\nDigite uma chave PIX.\\n\\nAdministrador:";
        }
    }

    if (strlen($pc63_banco) > 3) {
        $sqlerro = true;
        $erro_msg = "Usuário:\\n\\nBanco deve ter no máximo três(3) caracteres.\\n\\nAdministrador:";
    } elseif (strlen($pc63_agencia) > 5) {
        $sqlerro = true;
        $erro_msg = "Usuário:\\n\\nAgência deve ter no máximo cinco(5) caracteres.\\n\\nAdministrador:";
    } elseif (strlen($pc63_conta) > 12) {
        $sqlerro = true;
        $erro_msg = "Usuário:\\n\\nConta deve ter no máximo doze(12) caracteres.\\n\\nAdministrador.";
    }

    if (!$sqlerro) {
        db_inicio_transacao();

        if (isset($conferido)) {
            $clpcfornecon->pc63_dataconf = date("Y-m-d", db_getsession("DB_datausu"));
        }

        $clpcfornecon->pc63_codigooperacao = str_pad($pc63_codigooperacao, 4, "0", STR_PAD_LEFT);
        $clpcfornecon->pc63_chavepix = tratarChavePix($pc63_tipopix, $pc63_chavepix);
        $clpcfornecon->alterar($pc63_contabanco);
        $erro_msg = $clpcfornecon->erro_msg;

        if ($clpcfornecon->erro_status == 0) {
            $sqlerro = true;
        } else {
            $result = $clpcfornecon->sql_record($clpcfornecon->sql_query(null, "pc63_contabanco as contabco", "pc63_contabanco", "pc63_numcgm = $pc63_numcgm"));
            $numrows33 = $clpcfornecon->numrows;

            if ($pc64_contabanco == 't') {
                if ($result != 0 && $clpcfornecon->numrows) {
                    for ($i = 0; $i < $clpcfornecon->numrows; $i++) {
                        db_fieldsmemory($result, $i);
                        $clpcforneconpad->excluir($contabco);

                        if ($clpcforneconpad->erro_status == 0) {
                              $erro_msg = $clpcforneconpad->erro_msg;
                              $sqlerro = true;
                              break;
                        }
                    }
                }

                $clpcforneconpad->pc64_contabanco = $clpcfornecon->pc63_contabanco;
                $clpcforneconpad->incluir($clpcfornecon->pc63_contabanco);

                if ($clpcforneconpad->erro_status == 0) {
                    $erro_msg = $clpcforneconpad->erro_msg;
                    $sqlerro = true;
                }
            } else {
                if ($numrows33 > 0 && $padrao == "t") {
                    $erro_msg = "Alteração não efetuada.\\n Esta é a conta padrão. Selecione outra antes de alterá-la.";
                    $sqlerro  = true;
                }

                if ($sqlerro == false) {
                    $clpcforneconpad->excluir($pc63_contabanco);

                    if ($clpcforneconpad->erro_status == 0) {
                        $erro_msg = $clpcforneconpad->erro_msg;
                        $sqlerro = true;
                    }
                }
            }
        }
        db_fim_transacao($sqlerro);
    }
} elseif (isset($opcao)) {
    $result = $clpcfornecon->sql_record($clpcfornecon->sql_query($pc63_contabanco));

    if ($result != false && $clpcfornecon->numrows > 0) {
        db_fieldsmemory($result, 0);
        $result = $clpcforneconpad->sql_record($clpcforneconpad->sql_query_file($pc63_contabanco));

        if ($result != false && $clpcforneconpad->numrows > 0) {
            $pc64_contabanco = 't';
        } else {
            $pc64_contabanco = 'f';
        }
    }
}
?>

<html>
<?php
require_once(modification("forms/db_frmpcfornecon.php"));
?>

<script type="text/javascript">
  const toDateBr = (date) => {
    if (date instanceof Date) {
      return date.getDateBR();
    }
    return date.toString().getDate().getDateBR();
  }

  const popularGrid = (collection, collectionResponse, exclude = null) => {
    collectionResponse.map((lancamento) => {

      <?php if (isset($pc63_contabanco) && !isset($novo)) : ?>
        if (lancamento.codigo != <?php echo $pc63_contabanco ?>) {
          collection.add(lancamento);
        }
      <?php else : ?>
        collection.add(lancamento);
      <?php endif; ?>
    });
  }

  const carregaDadosLancamento = (tipo, linha) => {
    var url = "com1_pcfornecon001.php?";
    var parametros = "pc63_agencia=" + linha.agencia_solo;
    parametros += "&pc63_numcgm=" + linha.num_cgm;
    parametros += "&pc63_contabanco=" + linha.codigo;
    parametros += "&pc63_conta=" + linha.conta_solo;
    parametros += "&pc63_banco=" + linha.banco;
    parametros += "&db_opcao=2";
    parametros += "&opcao=" + tipo;
    location.href = url + parametros;
  }

  const carregaGridLancamentos = () => {
    const collectionLancamentos = new Collection().setId('codigo');
    var gridLancamentos = new DatagridCollection(collectionLancamentos).configure({
      order: false,
      height: 300
    });

    collectionLancamentos.clear();
    gridLancamentos.addColumn('codigo', {
      label: "Código Conta",
      width: '5%'
    });
    gridLancamentos.addColumn('cnpj_cpf', {
      label: "CNPJ/CPF",
      width: '30%',
      align: 'center'
    }).transform('cnpj');
    gridLancamentos.addColumn('banco', {
      label: "Banco",
      'width': '10%',
      align: 'center'
    });
    gridLancamentos.addColumn('agencia', {
      label: "Agência",
      'width': '10%',
      align: 'center'
    });
    gridLancamentos.addColumn('conta', {
      label: "Conta",
      'width': '15%',
      align: 'center'
    });
    gridLancamentos.addColumn('conferido', {
        label: "Conferido",
        'width': '10%',
        align: 'center'
      })
      .transform(function(conferido, item) {
        if (item.conferido) {
          return toDateBr(item.conferido);
        }
        return 'Não';
      });
    gridLancamentos.addColumn('conta_banco', {
        label: "Conta Padrão",
        'width': '10%',
        align: 'center'
      })
      .transform(function(conta_banco, item) {
        return item.conta_banco == 'Padrão' ? 'Padrão' : 'Não';
      });

    gridLancamentos.hideColumns([0]);
    gridLancamentos.addAction('Editar', 'Editar', (event, linha) => {
      carregaDadosLancamento('alterar', linha);
    }, true, 'fa-edit');

    gridLancamentos.addAction('Excluir', 'Excluir', (event, linha) => {

      if (!confirm("Deseja remover a conta?")) {
        return false;
      }

      const parametros = {};
      parametros.body = new FormData();
      parametros.body.append('acao', 'excluirLancamento');
      parametros.body.append('pc63_contabanco', linha.codigo);

      HttpClient.post('com1_pcfornecon.RPC.php', parametros).then(response => {
        if (response.erro) {
          alert(response.mensagem);
          return;
        }

        carregaGridLancamentos();
      });

    }, true, 'fa-trash');

    gridLancamentos.addAction('Informação', 'Informação', (event, linha) => {
      const tipos_pix = {
        2: 'CPF',
        3: 'CNPJ',
        4: 'Celular',
        5: 'E-mail',
        6: 'Aleatória'
      };

      let dateToBr = linha.conferido ? toDateBr(linha.conferido) : 'Não';
      let cnpj_cpf = js_formatar(linha.cnpj_cpf, "cpfcnpj");
      let dados = "Dados da Linha:\n\n";
      dados += "CNPJ/CPF: " + cnpj_cpf + "\n";
      dados += "Banco: " + linha.banco + "\n";
      dados += "Agência: " + linha.agencia + "\n";
      dados += "Conta: " + linha.conta + "\n";
      dados += "Conferido: " + dateToBr + "\n\n";
      if (linha.tipo_pix > 1 && linha.chave_pix != '') {
        const tipo_chave = tipos_pix[linha.tipo_pix] != undefined ? tipos_pix[linha.tipo_pix] : '';
        dados += "Dados do PIX: \n\n";
        dados += "Tipo de Chave PIX: " + tipo_chave + "\n";
        dados += "Chave PIX: " + linha.chave_pix + "\n";
      }
      alert(dados);

    }, true, 'fa-info-circle');

    const parametros = {};
    parametros.body = new FormData();
    parametros.body.append('acao', 'getLancamentos');
    parametros.body.append('num_cgm', '<?php echo $pc63_numcgm ?>');

    HttpClient.post('com1_pcfornecon.RPC.php', parametros).then(response => {
      if (response.erro) {
        alert(response.mensagem);
        return;
      }
      collectionLancamentos.clear();
      popularGrid(collectionLancamentos, response.lancamentos);
      gridLancamentos.reload();
    });

    gridLancamentos.show(document.getElementById('ctnGridLancamentos'));
  };

  carregaGridLancamentos();
</script>

</html>
<?php
if (isset($alterar) || isset($excluir) || isset($incluir)) {
    db_msgbox($erro_msg);
    if ($sqlerro == true) {
        echo "<script> document.form1." . $clpcfornecon->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1." . $clpcfornecon->erro_campo . ".focus();</script>";
    } elseif (isset($submita) && $sqlerro == false) {
        echo "<script> parent.document.form1.submit();</script>";
    } elseif (isset($reload) && !$sqlerro) {
        echo "<script>parent.ordem.location.href=parent.ordem.location;</script>";
        echo "<script>parent.db_iframe_pcfornecon.hide();</script>";
    } elseif (isset($alterar) || isset($incluir)) {
        echo "
    <script>
    var url       = 'com1_pcfornecon001.php?';      
    parametros    =  'pc63_numcgm=$pc63_numcgm';
    location.href = url+parametros;
    </script>";
    }
}
?>
