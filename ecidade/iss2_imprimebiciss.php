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

require_once(modification("libs/db_sql.php"));
require_once modification("libs/db_stdlib.php");
require_once modification("libs/db_conecta.php");
require_once modification("libs/db_sessoes.php");
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_escrito_classe.php"));
require_once(modification("classes/db_issquant_classe.php"));
$clescrito = new cl_escrito;
$clissquant = new cl_issquant;
$clrotulo = new rotulocampo;
$clrotulo->label("q177_setorfiscal");
$clrotulo->label("q02_inscr");
$clrotulo->label("z01_cgccpf");

db_postmemory($_POST);

$sql="select issbase.*,
             cg.z01_nome,
             cg.z01_nomecomple,
             cg.z01_numero,
             cg.z01_email,
             cg.z01_telef,
             cg.z01_cep,
             cg.z01_telcel,
             cg.z01_ident,
             cg.z01_bairro,
             cg.z01_munic,
             cg.z01_compl,
             cg.z01_numcgm,
             cg.z01_ender,
             cg.z01_incest,
             c.z01_nome as escritorio,
             cg.z01_nomefanta,
             j14_nome,
             j13_descr ,
             q02_numero,
             q02_compl,
             q05_matric,
             q14_proces,
             cg.z01_cgccpf,
             j88_descricao,
             ruas.j14_codigo,
             p58_numero,
             p58_ano,
             q40_descr,
             case
             when q123_situacao = 1
              and exists(select 1
                           from issmovalvara
                          where q120_issalvara = q123_sequencial
                        )
             then
               q98_descricao
             else
               ''
             end as q98_descricao,
             q167_descricao,
             j90_descr
      from issbase
             inner join cgm cg on cg.z01_numcgm = q02_numcgm
             left outer join issruas on issbase.q02_inscr = issruas.q02_inscr
             left outer join ruas on ruas.j14_codigo = issruas.j14_codigo
             left outer join issbairro on issbase.q02_inscr = q13_inscr
             left outer join bairro on j13_codi = q13_bairro
             left outer join escrito on issbase.q02_inscr = q10_inscr
             left outer join cgm c on c.z01_numcgm = q10_numcgm
             left outer join issmatric on issbase.q02_inscr = q05_inscr
             left outer join issprocesso on issbase.q02_inscr = q14_inscr
             left outer join ruastipo on j88_codigo = ruas.j14_tipo
             left outer join protprocesso on p58_codproc = q14_proces
             left join issbaseporte on issbaseporte.q45_inscr = issbase.q02_inscr
             left join issporte on issporte.q40_codporte = issbaseporte.q45_codporte
             inner join issalvara on issalvara.q123_inscr = issbase.q02_inscr
             inner join isstipoalvara on isstipoalvara.q98_sequencial = issalvara.q123_isstipoalvara
             left join issqn.formalocalvara on issbase.q02_formalocalvara = formalocalvara.q167_sequencial
             left join isssetorfiscal on isssetorfiscal.q177_issbase = issbase.q02_inscr
             left join setorfiscal on setorfiscal.j90_codigo = isssetorfiscal.q177_setorfiscal
      where issbase.q02_inscr = $inscr
      order by issalvara.q123_sequencial desc limit 1 ";

$result  = db_query($sql) or die($sql);
$numrows = pg_num_rows($result);

if($numrows>0){
  db_fieldsmemory($result,0,true);
}
if($numrows==0){
  db_redireciona("db_erros.php?fechar=true&db_erro=Nenhum registro encontrado.");
}

/**
 * Filtro de atividades
 *
 * 1 - todas
 * 2 - ativas
 * 3 - baixadas
 */
$filtroAtividades = '';
$filtroAtividadesLabel = 'Atividades: Todas';
if (isset($_GET['atividades']) && !empty($_GET['atividades'])) {
    switch ($_GET['atividades']) {
        case '2':
            $filtroAtividadesLabel = 'Atividades: Somente Ativas';
            $filtroAtividades = ' and q07_databx is null ';
            break;
        case '3':
            $filtroAtividadesLabel = 'Atividades: Somente Baixadas';
            $filtroAtividades = ' and q07_databx is not null ';
            break;
    }
}

$pdf = new ECidade\Pdf\Pdf();
$pdf->init(false);
$pdf->AliasNbPages();
$total = 0;
$alt = 4;
$pri = true;

$pdf->addTitulo("BIC Alvará");
$pdf->addTitulo("$RLq02_inscr: {$inscr}");
$pdf->addTitulo("CGM: {$z01_numcgm}");
$pdf->addTitulo($filtroAtividadesLabel);

for ($i = 0;$i < $numrows;$i++){
 db_fieldsmemory($result,$i);

 if (($pdf->gety() > $pdf->getH() -30)  || $pri==true ){
     $pdf->addpage("");
     $pdf->setfillcolor(235);
     $titulo = 9;
     $texto = 8;

     // novo dados cadastrais do CGM

     $pdf->setX(5);
     $pdf->SetFont('Arial','B',$titulo);
     $pdf->Cell(200,4,"Dados Cadastrais do CGM","LRBT",1,"C",0);
     $pdf->setX(5);
     $pdf->Cell(200,2,"","",1,"C",0);

     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Nome:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$z01_nome","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     if ( strlen(trim($z01_cgccpf)) == 14 ){
        $cpfcnpj = db_formatar($z01_cgccpf,"cnpj");
     }else if (strlen(trim($z01_cgccpf)) == 11){
        $cpfcnpj = db_formatar($z01_cgccpf,"cpf");
     }else{
        $cpfcnpj = $z01_cgccpf;
     }
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"$RLz01_cgccpf:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$cpfcnpj","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Endereço:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$z01_ender, Nº $z01_numero","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Complemento:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$z01_compl","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Bairro:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$z01_bairro","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     $telefone = '';
     if ( strlen(trim($z01_telef)) > 0 ){
        $telefone = $z01_telef;
     }
     if ( strlen(trim($z01_telcel)) > 0 ){
        $telefone = $telefone . ' / ' . $z01_telcel;
     }

     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Fone:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$telefone","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Cidade:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$z01_munic","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"E-mail:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$z01_email","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Cep:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,db_formatar($z01_cep,"cep"),"",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     $pdf->setX(120);
     $pdf->Cell(60,6,"","",1,"L",0);

     // fim
 }

 if (($pdf->gety() > $pdf->getH() -30)  || $pri==true ){
     $pdf->setfillcolor(235);
     $titulo = 9;
     $texto = 8;

     //lado esquerdo da tela
     $pdf->setX(5);
     $pdf->SetFont('Arial','B',$titulo);
     $pdf->Cell(200,4,"Dados Cadastrais do Alvará","LRBT",1,"C",0);
     $pdf->setX(5);
     $pdf->Cell(200,2,"","",1,"C",0);

     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"$RLq02_inscr:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$inscr","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Inscrição Estadual:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$z01_incest","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Nome:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(120,4,"$z01_nome","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Nome Completo:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(120,4,"$z01_nomecomple","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Nome Fantasia:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$z01_nomefanta","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Registro na junta:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q02_regjuc","",0,"L",0);
     //
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Protocolo da Junta:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q02_protocolojuntacomercial","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);
     //aqui

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Data da Junta:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,db_formatar($q02_dtjunta,"d"),"",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     $complemento = $q02_numero;
     if ( strlen(trim($q02_compl)) > 0 ){
         $complemento .= ' / ' . $q02_compl;
     }

     //lado direito da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Data do cadastro:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,db_formatar($q02_dtcada,"d"),"",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Data de início:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,db_formatar($q02_dtinic,"d"),"",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado esquerdo da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Data de Baixa:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,db_formatar($q02_dtbaix,"d"),"",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);


     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Logradouro:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$j14_codigo - $j88_descricao $j14_nome","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Número / Compl.:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$complemento","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado esquerdo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Bairro:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$j13_descr","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado direito da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Cep:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,db_formatar($q02_cep,"cep"),"",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     $pdf->MultiCell(30,5,"","",0,"L",1);

     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Forma de Localização:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q167_descricao","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     $result_area=$clissquant->sql_record($clissquant->sql_query_file(null,$q02_inscr,"q30_area,q30_quant,q30_tempofuncionamento,q30_areapublicidade, case when q30_graurisco = 'A' then 'ALTO'  when q30_graurisco = 'M' then 'MÉDIO' when q30_graurisco = 'B' then 'BAIXO' else '' end as q30_graurisco",null," q30_inscr = $q02_inscr and q30_anousu = ".db_getsession('DB_anousu')));

     if ($clissquant->numrows>0){
     	db_fieldsmemory($result_area,0);
     }

     //lado esquerdo da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Área:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q30_area","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado direito da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Empregados:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q30_quant","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado esquerdo da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Matrícula:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q05_matric","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado direito da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Tempo Funcionamento:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q30_tempofuncionamento","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     //lado esquerdo da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"$LSq177_setorfiscal:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$j90_descr","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado direito da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Controle - Protocolo:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q14_proces - $p58_numero/$p58_ano ","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     $sqlzona    = "select * from isszona inner join zonas on j50_zona = q35_zona where q35_inscr = $q02_inscr";
     $resultzona = db_query($sqlzona);
     $linhaszona = pg_num_rows($resultzona);
     if($linhaszona>0){
       db_fieldsmemory($resultzona,0);
     }

     //lado esquerdo da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Zona Fiscal:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q35_zona"."-".$j50_descr,"",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     //lado direito da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Referência Anterior:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q02_inscmu","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     // lado esquedo da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Tipo de Alvará:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q98_descricao","",1,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

     // lado esquedo da tela
     $pdf->setX(10);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(34,4,"Porte:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $pdf->Cell(60,4,"$q40_descr","",0,"L",0);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",0,"L",0);

     $result_escritorio = $clescrito->sql_record(
         $clescrito->sql_query(
             null,
             "q10_numcgm as cgm_esc,a.z01_nome as nome_esc",
             "q10_sequencial DESC",
             "q10_inscr = $inscr AND q10_dtfim IS NULL"
         ));
     if($clescrito->numrows>0){
          db_fieldsmemory($result_escritorio,0);
          $escri = $cgm_esc." - ".$nome_esc;
     }

     //lado direito da tela
     $pdf->setX(120);
     $pdf->SetFont('Arial','',$titulo);
     $pdf->Cell(30,4,"Contador:","",0,"L",1);
     $pdf->SetFont('Arial','',$texto);
     $y = $pdf->getY()+4;
      if(strlen($escri) < 35){
         $pdf->Cell(50,4,"$escri","",1,"L",0);
      }else {
         $pdf->MultiCell(50,4,$escri,"","J",0,0);
      }
      $pdf->setY($y);
     $pdf->Cell(30,1,"","",0,"R",0);
     $pdf->Cell(60,1,"","",1,"L",0);

  }
}

    // lado esquedo da tela
    $pdf->setX(10);
    $pdf->SetFont('Arial','',$titulo);
    $pdf->Cell(34,4,"Área de Publicidade:","",0,"L",1);
    $pdf->SetFont('Arial','',$texto);
    $pdf->Cell(60,4,"$q30_areapublicidade","",1,"L",0);
    $pdf->Cell(30,1,"","",0,"R",0);
    $pdf->Cell(60,1,"","",1,"L",0);

    $pdf->setX(10);
    $pdf->SetFont('Arial','',$titulo);
    $pdf->Cell(34,4,"Grau de Risco:","",0,"L",1);
    $pdf->SetFont('Arial','',$texto);
    $pdf->Cell(60,4,"$q30_graurisco","",1,"L",0);
    $pdf->Cell(30,1,"","",0,"R",0);
    $pdf->Cell(60,1,"","",1,"L",0);

$pdf->Cell(60,1,"","",1,"L",0);
if((isset($q02_obs) && $q02_obs != "") || (isset($q02_memo) && $q02_memo != "")){
     $pdf->setX(5);
     $pdf->SetFont('Arial','B',$titulo);
     $pdf->Cell(200,4,"Observações","LRBT",1,"C",0);
     $pdf->SetFont('Arial','','8');
     $pdf->MultiCell(190,4,$q02_obs.$q02_memo,"","J",0,0);
}

$sql = "select q07_ativ,
               q07_val_ativ_int,
               q07_imprimealvara,
               q03_descr,
               q07_datain,
               q07_datafi,
               q07_databx,
               q07_quant,
               tabativbaixa.*,
			case when q88_inscr is null then 'S'::char(1) else 'P'::char(1) end as q88_tipo,
               q11_processo,
			case when q11_oficio = 'true' then 'NORMAL'
				when q11_oficio = 'false' then 'OFICIO'
			     else ''
               end as q11_oficio,
                  case
                     when q71_classificacaorisco = 'A' or q71_classificacaorisco = 'a' then 'ALTO'
                     when q71_classificacaorisco = 'M' or q71_classificacaorisco = 'm' then 'MÉDIO'
                     when q71_classificacaorisco = 'B' or q71_classificacaorisco = 'b' then 'BAIXO'
                     else ''
                  end as q71_classificacaorisco
        from tabativ
                     inner join ativid on q07_ativ = q03_ativ
                     left join ativprinc on ativprinc.q88_inscr = tabativ.q07_inscr and ativprinc.q88_seq = tabativ.q07_seq
                     left join tabativbaixa on tabativ.q07_inscr = tabativbaixa.q11_inscr and tabativ.q07_seq = tabativbaixa.q11_seq
                     left join  atividcnae on q03_ativ = q74_ativid
                     left join  cnaeanalitica on  q72_sequencial = q74_cnaeanalitica
                     left join  cnae on q71_sequencial = q72_cnae
        where q07_inscr = $inscr {$filtroAtividades}
        order by case when q88_inscr is null then 2 else 1 end, q07_datain, q07_datafi
        ";

$result  = db_query($sql);
$numrows = pg_num_rows($result);

$pdf->setX(5);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(200,4,"Atividades","LRBT",1,"C",0);
$pdf->setX(5);
$pdf->Cell(200,2,"","",1,"C",0);

if($numrows <> 0){
   $pdf->setX(10);
   $pdf->SetFont('Arial','',$titulo);
   $pdf->cell(15,4,"Cod.",0,0,"C",1);
   $pdf->cell(20,4,"Ativ. Interna",0,0,"C",1);
   $pdf->cell(20,4,"Imp. Alvará",0,0,"C",1);
   $pdf->cell(62,4,"Atividade",0,0,"C",1);
   $pdf->cell(6,4,"Tipo",0,0,"C",1);
   $pdf->cell(20,4,"Data Inicio",0,0,"C",1);
   $pdf->cell(20,4,"Data Fim",0,0,"C",1);
   $pdf->cell(20,4,"Data Baixa",0,0,"C",1);
   $pdf->cell(10,4,"Risco",0,1,"C",1);


    for ($i = 0;$i < $numrows;$i++){
      db_fieldsmemory($result,$i);
      $y = $pdf->getY();
      $pdf->setX(10);
      $pdf->SetFont('Arial','',$texto);
      $pdf->cell(15,4,"$q07_ativ",0,0,"C",0);
      $pdf->cell(20,4,"$q07_val_ativ_int",0,0,"C",0);
      $pdf->cell(20,4,"$q07_imprimealvara",0,0,"C",0);
      $pdf->multicell(64,4,"$q03_descr",0,"L",0);
      $ym = $pdf->getY();
      if($y > 270 ){
         $y = 35;
      }
      $pdf->setY($y);
      $pdf->setX(128);
      $pdf->cell(5,4,$q88_tipo,0,0,"L",0);
      $pdf->cell(20,4,db_formatar($q07_datain,"d"),0,0,"C",0);
      $pdf->cell(20,4,db_formatar($q07_datafi,"d"),0,0,"C",0);
      $pdf->cell(20,4,db_formatar($q07_databx,"d"),0,0,"C",0);
      $pdf->cell(10,4,$q71_classificacaorisco,0,1,"R",0);
      // Quebra linhas para descrições com muitos caracteres
      $iTotalCaracterDescricao = strlen($q03_descr);
      $iTotalCaracterLinha =  35;
      if($iTotalCaracterDescricao >= $iTotalCaracterLinha){
         for ($ii = 0;$ii < ($iTotalCaracterDescricao - $iTotalCaracterLinha);$ii+=$iTotalCaracterLinha){
            $pdf->ln();
         }
      }
      // end - *Quebra linhas para descrições com muitos caracteres*

      if(isset($q11_obs) && $q11_obs != ""){
         $pdf->multicell(190,4,"Observações da baixa - $q11_obs  ",0,"L","L",0);
         $ym = $pdf->getY();
      }
      $pdf->setY($ym);
    }
}else{
    if (!empty($filtroAtividades)) {
        db_redireciona("db_erros.php?db_erro=Nenhum registro encontrado com os filtros: {$filtroAtividadesLabel}");
    }

    $pdf->cell(190,4,"NÃO POSSUI ATIVIDADE",0,1,"C",0);
}

$sql="select cgmsocio.z01_numcgm,
             cgmsocio.z01_nome,
	        cgmsocio.z01_ender,
	        cgmsocio.z01_munic,
	        q95_perc
      from issbase
	     inner join socios on q95_cgmpri = q02_numcgm
	     inner join cgm cgmsocio on cgmsocio.z01_numcgm = q95_numcgm
	     inner join cgm cgmempresa on cgmempresa.z01_numcgm = q02_numcgm where q95_tipo in (1,2,3) and q02_inscr =$inscr";

$result = db_query($sql);
$numrows = pg_num_rows($result);

$pdf->Cell(200,2,"","",1,"C",0);
$pdf->setX(5);
$pdf->SetFont('Arial','B',$titulo);
$pdf->Cell(200,4,"Sócios / Responsável","LRBT",1,"C",0);
$pdf->setX(5);
$pdf->Cell(200,2,"","",1,"C",0);

if($numrows <> 0){
   $pdf->setX(10);
   $pdf->SetFont('Arial','',$titulo);
   $pdf->cell(15,4,"CGM",0,0,"C",1);
   $pdf->cell(65,4,"Nome",0,0,"C",1);
   $pdf->cell(65,4,"Endereço",0,0,"C",1);
   $pdf->cell(28,4,"Município",0,0,"C",1);
   $pdf->cell(22,4,"Valor do Capital",0,1,"C",1);


   for ($i = 0;$i < $numrows;$i++){
      db_fieldsmemory($result,$i);

      $q95_perc = db_formatar($q95_perc, 'f');

      $pdf->setX(10);
      $pdf->SetFont('Arial','',$texto);
      $pdf->cell(15,4,"$z01_numcgm",0,0,"C",0);
      $pdf->cell(65,4,"$z01_nome",0,0,"L",0);
      $pdf->cell(65,4,"$z01_ender",0,0,"L",0);
      $pdf->cell(28,4,"$z01_munic",0,0,"C",0);
      $pdf->cell(22,4,"$q95_perc",0,1,"C",0);
   }
}
else{
  $pdf->cell(190,4,"NÃO POSSUI SOCIOS",0,1,"C",0);
}

$sql     = "select * from aidof left join aidofproc on y02_aidof = y08_codigo where y08_inscr = $inscr";
$result  = db_query($sql);
$numrows = pg_num_rows($result);

$pdf->Cell(200,2,"","",1,"C",0);
$pdf->setX(5);
$pdf->SetFont('Arial','B',$titulo);
$pdf->Cell(200,4,"Aidof","LRBT",1,"C",0);
$pdf->setX(5);
$pdf->Cell(200,2,"","",1,"C",0);

if ($numrows <> 0) {
   $pdf->setX(10);
   $pdf->SetFont('Arial','',$titulo);
   $pdf->cell(10,4,"Código",0,0,"C",1);
   $pdf->cell(20,4,"Processo",0,0,"C",1);
   $pdf->cell(30,4,"Data Lançamento",0,0,"C",1);
   $pdf->cell(20,4,"Nota Inicial",0,0,"C",1);
   $pdf->cell(30,4,"Quant. Solicitada",0,0,"C",1);
   $pdf->cell(30,4,"Quant. Liberada",0,0,"C",1);
   $pdf->cell(20,4,"Nota Final",0,0,"C",1);
   $pdf->cell(20,4,"Gráfica",0,0,"C",1);
   $pdf->cell(10,4,"Cancel.",0,1,"C",1);
   for ($i = 0; $i < $numrows;$i++) {
      db_fieldsmemory($result,$i);
      $pdf->setX(10);
      $pdf->SetFont('Arial','',$texto);
      $p=0;
      if ($y08_cancel=="t"){
      	$cancel="Sim";
      }else{
      	$cancel="Não";
      }
      $pdf->cell(10,4,$y08_codigo,0,0,"C",$p);
      $pdf->cell(20,4,$y02_codproc,0,0,"C",$p);
      $pdf->cell(30,4,db_formatar($y08_dtlanc,"d"),0,0,"C",$p);
      $pdf->cell(20,4,$y08_notain,0,0,"C",$p);
      $pdf->cell(30,4,$y08_quantsol,0,0,"C",$p);
      $pdf->cell(30,4,$y08_quantlib,0,0,"C",$p);
      $pdf->cell(20,4,$y08_notafi,0,0,"C",$p);
      $pdf->cell(20,4,$y08_numcgm,0,0,"C",$p);
      $pdf->cell(10,4,$cancel,0,1,"C",$p);
   }
}
else{
  $pdf->cell(190,4,"NÃO POSSUI AIDOF",0,1,"C",0);
}

/*
 * Bloco que testa se a empresa é optante do simples
 */

$sql  = "SELECT isscadsimples.q38_sequencial,                                                                          ";
$sql .= "       isscadsimples.q38_dtinicial,                                                                           ";
$sql .= "       CASE                                                                                                   ";
$sql .= "         WHEN isscadsimples.q38_categoria = 1 THEN 'Micro Empresa'                                            ";
$sql .= "         WHEN isscadsimples.q38_categoria = 2 THEN 'Empresa de pequeno porte'                                 ";
$sql .= "         WHEN isscadsimples.q38_categoria = 3 THEN 'MEI'                                                      ";
$sql .= "         WHEN isscadsimples.q38_categoria = 4 THEN 'EIRELI'                                                   ";
$sql .= "         WHEN isscadsimples.q38_categoria = 5 THEN 'Soc. Profissionais'                                       ";
$sql .= "       END AS q38_categoria,                                                                                  ";
$sql .= "       isscadsimplesbaixa.q39_dtbaixa,                                                                        ";
$sql .= "       isscadsimplesbaixa.q39_issmotivobaixa,                                                                 ";
$sql .= "       isscadsimplesbaixa.q39_obs,                                                                            ";
$sql .= "       issmotivobaixa.q42_descr                                                                               ";
$sql .= "  FROM isscadsimples                                                                                          ";
$sql .= "       LEFT JOIN isscadsimplesbaixa ON isscadsimples.q38_sequencial  = isscadsimplesbaixa.q39_isscadsimples   ";
$sql .= "       LEFT JOIN issmotivobaixa     ON issmotivobaixa.q42_sequencial = isscadsimplesbaixa.q39_issmotivobaixa  ";
$sql .= " WHERE isscadsimples.q38_inscr = {$inscr}                                                                     ";

$result  = db_query($sql);
$numrows = pg_num_rows($result);
$pdf->Cell(200,2,"","",1,"C",0);
$pdf->setX(5);
$pdf->SetFont('Arial','B',$titulo);
$pdf->Cell(200,4,"Optante Simples","LRBT",1,"C",0);
$pdf->setX(5);
$pdf->Cell(200,2,"","",1,"C",0);

if ($numrows <> 0) {

  $pdf->setX(10);
  $pdf->SetFont('Arial','',$titulo);
  $pdf->cell(10,4,"Código",0,0,"C",1);
  $pdf->cell(20,4,"Data Inicial",0,0,"C",1);
  $pdf->cell(30,4,"Categoria",0,0,"C",1);
  $pdf->cell(20,4,"Data da baixa",0,0,"C",1);
  $pdf->cell(40,4,"Motivo da baixa",0,0,"C",1);
  $pdf->cell(70,4,"Observações",0,1,"C",1);
  for ($i = 0; $i < $numrows; $i++) {
  	db_fieldsmemory($result,$i);
    $pdf->setX(10);
    $pdf->SetFont('Arial','',$texto);
    $p=0;
    $pdf->cell(10, 4, $q38_sequencial, 0, 0, "C", $p);
    $pdf->cell(20, 4, db_formatar($q38_dtinicial, 'd'), 0, 0, "C", $p);
    $pdf->cell(30, 4, $q38_categoria, 0, 0, "C", $p);
    $pdf->cell(20, 4, db_formatar($q39_dtbaixa, 'd'), 0, 0, "C", $p);
    $pdf->cell(40, 4, $q42_descr, 0, 0, "C", $p);
    $pdf->cell(70, 4, $q39_obs, 0, 1, "C", $p);
  }
} else {
	$pdf->cell(190,4,"Sem lançamentos",0,1,"C",0);
}


$sCampoMov  = "q120_sequencial,                "; // 1
$sCampoMov  = "q121_descr as dl_Movimentacao,                "; // 1
$sCampoMov .= "q120_dtmov as dl_data,                        "; // 2
$sCampoMov .= "case                                          "; // 4
$sCampoMov .= "  when q123_situacao = 1                      "; // 4
$sCampoMov .= "    then  'Ativo'                             "; // 4
$sCampoMov .= "  else 'Inativo'                              "; // 4
$sCampoMov .= "end as dl_situacao,                           "; // 4
$sCampoMov .= "q120_validadealvara ||' Dias' as dl_validade, "; // 5
$sCampoMov .= "q124_codproc as dl_processo,                  "; // 6
$sCampoMov .= "login as dl_Login,                            "; // 7
$sCampoMov .= "q120_obs                                      "; // 8

$sSqlMovAlvara  = " select {$sCampoMov} ";
$sSqlMovAlvara .= "   from issmovalvara ";
$sSqlMovAlvara .= "       inner join isstipomovalvara on isstipomovalvara.q121_sequencial = issmovalvara.q120_isstipomovalvara  ";
$sSqlMovAlvara .= "       inner join issalvara on issalvara.q123_sequencial = issmovalvara.q120_issalvara                       ";
$sSqlMovAlvara .= "       inner join issbase on issbase.q02_inscr = issalvara.q123_inscr                                        ";
$sSqlMovAlvara .= "       inner join isstipoalvara on isstipoalvara.q98_sequencial = issalvara.q123_isstipoalvara               ";
$sSqlMovAlvara .= "    inner join db_usuarios on id_usuario = q120_usuario                                                   ";
$sSqlMovAlvara .= "     left join issmovalvaraprocesso on q124_issmovalvara = q120_sequencial  ";
$sSqlMovAlvara .= "       where q123_inscr = {$inscr}                                                                 ";
$sSqlMovAlvara .= "      order by q120_sequencial desc                                                                          ";

$result = db_query($sSqlMovAlvara);
$numrows = pg_num_rows($result);

$pdf->Cell(200,2,"","",1,"C",0);
$pdf->setX(5);
$pdf->SetFont('Arial','B',$titulo);
$pdf->Cell(200,4,"Movimentações Alvará","LRBT",1,"C",0);
$pdf->setX(5);
$pdf->Cell(200,2,"","",1,"C",0);

if($numrows <> 0){
   $pdf->setX(10);
   $pdf->SetFont('Arial','',$titulo);
   $pdf->cell(30,4,"Movimentação",0,0,"L",1);
   $pdf->cell(16,4,"Data",0,0,"C",1);
   $pdf->cell(20,4,"Situação",0,0,"C",1);
   $pdf->cell(20,4,"Validade",0,0,"C",1);
   $pdf->cell(30,4,"Processo",0,0,"C",1);
   $pdf->cell(30,4,"Login",0,0,"C",1);
   $pdf->cell(49,4,"Observação",0,1,"L",1);

   for ($i = 0;$i < $numrows;$i++){
      db_fieldsmemory($result,$i);
      $pdf->setX(10);
      $pdf->SetFont('Arial','',$texto);
      $pdf->cell(30,4,"$dl_movimentacao",0,0,"L",0);
      $pdf->cell(16,4,db_formatar($dl_data, "d"),0,0,"C",0);
      $pdf->cell(20,4,"$dl_situacao",0,0,"C",0);
      $pdf->cell(20,4,"$dl_validade",0,0,"C",0);
      $pdf->cell(30,4,"$dl_processo",0,0,"C",0);
      $pdf->cell(30,4,"$dl_login",0,0,"C",0);
      $pdf->MultiCell(49,4,"$q120_obs",0,1,"L",0);
   }
}
else{
  $pdf->cell(190,4,"NÃO POSSUI MOVIMENTAÇÕES",0,1,"C",0);
}

$sSqlParalisacoes = "select
                     q140_datainicio as dl_data_inicial,
                     q140_datafim as dl_data_final,
                     q140_usuario as dl_usuario,
                     q141_descricao as dl_motivo,
                     q140_observacao as dl_obs
                    from
                     issbaseparalisacao
                    inner join issmotivoparalisacao on
                     q141_sequencial = q140_issmotivoparalisacao
                    where q140_issbase = $inscr";

$result = db_query($sSqlParalisacoes);
$numrows = pg_num_rows($result);

$pdf->Cell(200,2,"","",1,"C",0);
$pdf->setX(5);
$pdf->SetFont('Arial','B',$titulo);
$pdf->Cell(200,4,"Histórico de Paralisações","LRBT",1,"C",0);
$pdf->setX(5);
$pdf->Cell(200,2,"","",1,"C",0);

if($numrows <> 0){
   $pdf->setX(10);
   $pdf->SetFont('Arial','',$titulo);
   $pdf->cell(30,4,"Data Inicial",0,0,"C",1);
   $pdf->cell(30,4,"Data Final",0,0,"C",1);
   $pdf->cell(20,4,"Usuário",0,0,"C",1);
   $pdf->cell(50,4,"Motivo",0,0,"C",1);
   $pdf->cell(65,4,"Observação",0,0,"L",1);
   $pdf->Ln();

   for ($i = 0;$i < $numrows;$i++){
      db_fieldsmemory($result,$i);
      $pdf->setX(10);
      $pdf->SetFont('Arial','',$texto);
      $pdf->cell(30,4,db_formatar($dl_data_inicial, "d"),0,0,"C",0);
      $pdf->cell(30,4,db_formatar($dl_data_final, "d"),0,0,"C",0);
      $pdf->cell(20,4,"$dl_usuario",0,0,"C",0);
      $pdf->cell(50,4,"$dl_motivo",0,0,"C",0);
      $pdf->MultiCell(65,4,"$dl_obs",0,1,"L",0);
   }
}
else{
  $pdf->cell(190,4,"NÃO POSSUI PARALISAÇÕES",0,1,"C",0);
}

$pdf->Output('I');
