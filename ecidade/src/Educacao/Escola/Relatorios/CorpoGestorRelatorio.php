<?php

namespace ECidade\Educacao\Escola\Relatorios;

use ECidade\Tributario\Cadastro\Model\Bairro;
use ECidade\Tributario\Cadastro\Registry\BairroRegistry;
use FpdfMultiCellBorder;
use stdClass;

/**
 * Class CorpoGestorRelatorio
 * @package ECidade\Educacao\Escola\Relatorios
 */
class CorpoGestorRelatorio extends FpdfMultiCellBorder
{
    /**
     * @var stdClass
     */
    private $parametros;
    /**
     * @var Bairro|Null
     */
    private $bairro;

    public function __construct($parametros, $filtros_cabecalho)
    {
        parent::__construct();
        $this->parametros = $parametros;

        if ($parametros->opt_escola == 1) {
            $this->parametros->opt_escola = "Escolas";
        } elseif ($parametros->opt_escola == 2) {
            $this->parametros->opt_escola = "CEIs";
        } else {
            $this->parametros->opt_escola = "Escolas e CEIs";
        }

        global $head2;
        global $head4;
        global $head5;
        global $head7;

        $sNomeDistrito = utf8_decode($this->parametros->nome_distrito);
        $sCodBairro = utf8_decode($this->parametros->codEscolaBairro);

        if (!empty($filtros_cabecalho)) {
            $head2 = "Relatório de Endereço por: {$filtros_cabecalho}";
        }
        if (!empty($this->parametros->nome_distrito)) {
            $head4 = "Distrito: {$sNomeDistrito}";
        }

        if (!empty($this->parametros->codEscolaBairro)) {
            $head5 = "Codigo/Escola/Bairro: {$sCodBairro}";
        }
        $head7 = $this->parametros->opt_escola;
    }

    protected function opem()
    {
        $this->mostrarRodape(true);
        $this->mostrarTotalDePaginas(true);
        $this->mostrarEmissor(true);
        $this->SetMargins(10, 8, 8);
        $this->Open();
        $this->exibeHeader(true);
        $this->SetAutoPageBreak(true, 10);
        $this->AliasNbPages();
        $this->SetFillColor(235);
    }

    public function emitir()
    {
        $this->opem();
        $this->AddPage();

        $sCodBairro = utf8_decode($this->parametros->codEscolaBairro);

        $where = "";
        if (!is_null($this->bairro)) {
            $where = " AND j13_codi = {$this->bairro->getCodigo()} ";
        }

        if ($this->parametros->chk_escolas_bairro and !is_numeric($sCodBairro)) {
            $where .= " AND fc_remove_acentos(j13_descr) ilike fc_remove_acentos('%{$sCodBairro}%') ";
        }

        $zona = intval($this->parametros->zona);
        if ($zona === 2) {
            $where .= " AND j13_rural = true";
        } elseif ($zona === 1) {
            $where .= " AND j13_rural = false";
        }

        $sql_bairro = "
        SELECT j13_codi, j13_descr
          FROM bairro
              WHERE exists(
                  SELECT 1 FROM escola
                    WHERE j13_codi = ed18_i_bairro
                      AND ed18_i_funcionamento = 1 AND ed18_i_tipoescola = 1
                  )
          {$where}
          ORDER BY 2
        ";
        $result_bairro = db_query($sql_bairro);

        if (!$this->parametros->chk_escolas_bairro) {
            while ($ln = pg_fetch_array($result_bairro)) {
                $this->SetFont('Arial', 'B', 9);
                if ($this->getAvailHeight() <= 22) {
                    $this->AddPage();
                }
                if ($this->parametros->chk_escolas_bairro || !is_null($this->bairro)) {
                    $this->Cell(192, 10, $ln['j13_descr'], 0, 1);
                }
            }
            $this->rodarSql();
        } else {
            while ($ln = pg_fetch_array($result_bairro)) {
                $this->SetFont('Arial', 'B', 9);
                if ($this->getAvailHeight() <= 22) {
                    $this->AddPage();
                }
                if ($this->parametros->chk_escolas_bairro || !is_null($this->bairro)) {
                    $this->Cell(192, 10, $ln['j13_descr'], 0, 1);
                }
                $this->rodarSql($ln['j13_codi']);
            }
        }

        $this->Output();
    }

    private function rodarSql($bairro = null)
    {
        $cont = 0;
        $codigos_calendarios = 0;
        if (!empty($this->parametros->calendarios)) {
            $codigos_calendarios = implode("','", $this->parametros->calendarios);
        }

        $sql = "SELECT ed18_i_codigo as codigo_escola,
                    ed18_c_nome as escola,
                    ed18_codigoreferencia as codigo_ref,
                    j14_nome as logradouro,
                    ed18_i_numero as numero,
                    ed18_c_compl as complemento,
                    j13_descr as bairro,
                    ed18_c_cep as cep,
                    telefone as telefones,
                    ddd as ddds,j13_codi
                FROM (SELECT  ed18_codigoreferencia,
                                ed18_i_codigo,
                                ed18_c_nome,
                                j14_nome,
                            ed18_i_numero,
                            ed18_c_compl,
                            j13_codi,
                            j13_descr,
                            ed18_c_cep,
                            z01_nome,
                                (select array_to_string(telefone[1:2], ',')
                            from (select array_accum(ed26_i_numero)as telefone from telefoneescola
                                        where ed26_i_escola = escola.ed18_i_codigo group by ed26_i_escola) as telefones
                            LIMIT 1) as telefone,

                    (select array_to_string(ddd[1:2], ',')
                            from (select array_accum(ed26_i_ddd)as ddd  from telefoneescola
                                        where ed26_i_escola = escola.ed18_i_codigo group by ed26_i_ddd ) as ddds
                            LIMIT 1) as ddd
                        FROM escola.escola
                        INNER JOIN   censodistrito on ed18_i_censodistrito = ed262_i_codigo
                            INNER JOIN ruas ON ed18_i_rua		= j14_codigo
                            INNER JOIN bairro ON ed18_i_bairro	= j13_codi
                            LEFT join rechumanoescola on ed75_i_escola = escola.ed18_i_codigo
                            LEFT join rechumanoativ on ed22_i_rechumanoescola = ed75_i_codigo
                            LEFT JOIN rechumano ON ed20_i_codigo	= rechumanoescola.ed75_i_rechumano
                            LEFT join atividaderh on ed01_i_codigo = ed22_i_atividade
                            LEFT JOIN rechumanopessoal ON ed284_i_rechumano	= ed20_i_codigo
                            LEFT JOIN rhpessoal ON ed284_i_rhpessoal	 = rh01_regist
                            left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
                            left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
                ";
        $sqlRh = "";
        if (($this->parametros->chk_diretor ||
            $this->parametros->chk_diretor_interino ||
            $this->parametros->chk_orientador ||
            $this->parametros->chk_diretor_adjunto) && !$this->parametros->chk_funcional) {
            $sqlRh .= " AND ( ed01_i_codigo = 0 ";
            if ($this->parametros->chk_diretor) {
                $sqlRh .= " or ed01_i_codigo = 5 ";
            }

            if ($this->parametros->chk_diretor_adjunto) {
                $sqlRh .= " or ed01_i_codigo = 14 ";
            }

            if ($this->parametros->chk_diretor_interino) {
                $sqlRh .= " or ed01_i_codigo = 7 ";
            }

            if ($this->parametros->chk_orientador) {
                $sqlRh .= " or ed01_i_codigo = 11 ";
            }
            $sqlRh .= ") ";
        }

        $sql .= "  WHERE  ed18_i_funcionamento = 1 AND ed18_i_tipoescola = 1";
        $sql .= " AND rechumanoescola.ed75_i_saidaescola IS NULL {$sqlRh} ";

        $sNomeDistrito = utf8_decode($this->parametros->nome_distrito);
        if (!empty($this->parametros->nome_distrito)) {
            $sql .= "and ed262_c_nome ilike fc_remove_acentos('%{$sNomeDistrito}%')";
        }

        $zona = intval($this->parametros->zona);

        if ($zona === 2) {
            $sql .= " AND j13_rural = true";
        } elseif ($zona === 1) {
            $sql .= " AND j13_rural = false";
        }

        $where = [];
        $sql .= "  ORDER BY ed18_codigoreferencia) as tmpgeradorrelatorio1804200835  ";
        if ($this->parametros->opt_escola == "Escolas") {
            $where[] = "ed18_codigoreferencia < 3000 ";
        }
        if ($this->parametros->opt_escola == "CEIs") {
            $where[] = "ed18_codigoreferencia >=3000 ";
        }
        if (!empty($this->parametros->codEscolaBairro) && is_numeric($this->parametros->codEscolaBairro)) {
            $where[] = "ed18_codigoreferencia = {$this->parametros->codEscolaBairro}";
        }

        $sCodBairro = utf8_decode($this->parametros->codEscolaBairro);
        if (!empty($this->parametros->codEscolaBairro && !is_numeric($this->parametros->codEscolaBairro))) {
            $where[] = "(
                fc_remove_acentos(ed18_c_nome) ilike fc_remove_acentos('%{$sCodBairro}%') OR
                fc_remove_acentos(j13_descr) ilike fc_remove_acentos('%{$sCodBairro}%')
            )";
        }
        if (!is_null($bairro)) {
            $where[] = "j13_codi = {$bairro} ";
        }

        if (!empty($where)) {
            $where = implode(' and ', $where);
            $sql .= " where {$where}";
        }

        $sql .= "  group by codigo_escola, escola, codigo_ref, logradouro, numero, complemento, bairro, cep,
                        telefones, ddds,j13_codi ORDER BY ed18_codigoreferencia asc ";


        $result = db_query($sql);
        if (!$result) {
            db_redireciona('db_erros.php?db_erro=Erro ao buscar dados.');
            exit;
        }

        $html = "";
        while ($row = pg_fetch_array($result)) {
            $cont++;

            $codigo_escola = $row['codigo_escola'];
            $escola = $row['escola'];
            $codigo_ref = $row['codigo_ref'];
            $logradouro = $row['logradouro'];
            $numero = $row['numero'];
            $complemento = $row['complemento'];
            $bairro = $row['bairro'];

            $cep = trim($row['cep']);

            if (strlen($cep) == 8) {
                $cep = substr($cep, 0, 5) . "-" . substr($cep, 5);
            }

            $telefone = $row['telefones'];

            //Wallace 2018-05-17
            $ddd = $row['ddds'];
            $ddd1 = "";
            $ddd2 = "";

            if ($ddd != "") {
                if (strlen($ddd) > 2) {
                    $pos = strpos($ddd, ',');

                    if ($pos > 0) {
                        $ddd1 = trim(substr($ddd, 0, $pos));
                        $ddd2 = trim(substr($ddd, $pos + 1));
                    }

                    $ddd1 = $ddd1;
                    $ddd2 = $ddd2;
                } else {
                    $ddd = $ddd;
                }
            }

            if ($telefone != "") {
                if (strlen($telefone) > 13) {
                    $pos = strpos($telefone, ',');

                    if ($pos > 0) {
                        $telefone1 = trim(substr($telefone, 0, $pos));
                        $telefone2 = trim(substr($telefone, $pos + 1));
                    }

                    $telefone1 = $this->maskFoneDdd($telefone1, $ddd1);
                    $telefone2 = $this->maskFoneDdd($telefone2, $ddd2);

                    $telefone = $telefone1 . ", " . $telefone2;
                } else {
                    $telefone = $this->maskFoneDdd($telefone, $ddd);
                }
            }

            $this->SetFont('Arial', 'B', 8);
            if ($this->getAvailHeight() <= 12) {
                $this->AddPage();
            }
            if ($this->parametros->chk_escolas_bairro || !is_null($this->bairro)) {
                $this->Cell(12, 4, $cont);
            }
            $this->Cell(50, 4, "Cod: {$codigo_ref}");
            $this->Cell(100, 4, "{$escola}");

            if ($this->parametros->chk_alunos) {
                $qtd_alunos = $this->getQuantidadeAlunosEscola($codigo_escola, $codigos_calendarios);
                $this->Cell(30, 4, "N. de Alunos: {$qtd_alunos}", 0, 1, 'R');
            } else {
                $this->Cell(30, 4, "", 0, 1);
            }

            $this->SetFont('Arial', '', 8);
            $logradouro = trim($logradouro);
            $this->Cell(80, 4, "{$logradouro},  {$numero}");
            $this->Cell(52, 4, "CEP: {$cep}");
            $this->Cell(60, 4, "ECidade: {$codigo_escola}", 0, 1, 'R');

            $this->Cell(132, 4, "BAIRRO: {$bairro}", 0, 0);
            $this->Cell(60, 4, "TELEFONE: {$telefone}", 0, 1);

            $this->Cell(192, 2, "", 0, 1);

            $sql = "SELECT case when ed20_i_tiposervidor = 1
                                then rechumanopessoal.ed284_i_rhpessoal
                                else rechumanocgm.ed285_i_cgm
                            end as ed20_i_codigo,
	               case when ed20_i_tiposervidor = 1 then cgmrh.z01_nome else cgmcgm.z01_nome end as z01_nome,
	               case when ed20_i_tiposervidor = 1 then cgmrh.z01_cgccpf else cgmcgm.z01_cgccpf end  as z01_cgccpf,
                   case when ed20_i_tiposervidor = 1 then cgmfisico.z04_nomesocial 
                   else cgmfisico.z04_nomesocial end as z04_nomesocial,
	               ed01_c_descr,
	               case when ed20_i_tiposervidor = 1
	                then regimerh.rh30_descr
	                else regimecgm.rh30_descr
	               end as rh30_descr,
	               ed75_i_saidaescola,
					cgmrh.z01_telef,ed30_i_ramal,

					(select array_to_string(celular[1:2], ',')
               from (select array_accum(ed30_i_numero)as celular from telefonerechumano
                        where ed30_i_rechumano = ed20_i_codigo
                          AND ed30_i_tipotelefone = 1
                    group by ed30_i_rechumano) as celulares
              ) as celular,

              (select array_to_string(ddd[1:2], ',')
               from (select array_accum(ed30_i_ramal)as ddd
                    from telefonerechumano
                        where ed30_i_rechumano = ed20_i_codigo AND ed30_i_tipotelefone = 1
                        group by ed30_i_rechumano) as ddds
              ) as ddd

	        FROM rechumano
	         left join telefonerechumano on ed30_i_rechumano = ed20_i_codigo
	         inner join rechumanoescola on ed75_i_rechumano = ed20_i_codigo
	         left join rechumanoativ on ed22_i_rechumanoescola = ed75_i_codigo
	         left join atividaderh on ed01_i_codigo = ed22_i_atividade
	         left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
	         left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
	         left join rhpessoalmov on rhpessoalmov.rh02_anousu  = 2014
	                               and rhpessoalmov.rh02_mesusu  = 08
	                               and rhpessoalmov.rh02_regist  = rhpessoal.rh01_regist
	                               and rhpessoalmov.rh02_instit  = 1
	         left join rhregime as regimerh on  regimerh.rh30_codreg = rhpessoalmov.rh02_codreg
	         left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
	         left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
	         left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
	         left join rhregime as regimecgm on  regimecgm.rh30_codreg = rechumano.ed20_i_rhregime
             left join cgmfisico on cgmfisico.z04_numcgm = rhpessoal.rh01_numcgm
	        WHERE ed75_i_escola = '{$codigo_escola}' ";

            if (($this->parametros->chk_diretor ||
                    $this->parametros->chk_diretor_interino ||
                    $this->parametros->chk_orientador ||
                    $this->parametros->chk_diretor_adjunto
                ) && !$this->parametros->chk_funcional) {
                $sql .= " AND (ed01_i_codigo=0 ";

                if ($this->parametros->chk_diretor) {
                    $sql .= " or ed01_i_codigo = 5 ";
                }

                if ($this->parametros->chk_diretor_adjunto) {
                    $sql .= " or ed01_i_codigo = 14 ";
                }

                if ($this->parametros->chk_diretor_interino) {
                    $sql .= " or ed01_i_codigo = 7 ";
                }

                if ($this->parametros->chk_orientador) {
                    $sql .= " or ed01_i_codigo = 11 ";
                }

                $sql .= ")";
            } elseif ((!$this->parametros->chk_diretor &&
                    !$this->parametros->chk_diretor_interino &&
                    !$this->parametros->chk_orientador &&
                    !$this->parametros->chk_diretor_adjunto) && !$this->parametros->chk_funcional) {
                $sql .= " AND 1=2 ";
            } elseif (($this->parametros->chk_diretor &&
                    $this->parametros->chk_diretor_interino &&
                    $this->parametros->chk_orientador &&
                    $this->parametros->chk_diretor_adjunto) && $this->parametros->chk_funcional) {
            }

            $sql .= "AND ed75_i_saidaescola is null group by ed20_i_codigo,
                    cgmcgm.z01_nome, cgmfisico.z04_nomesocial, cgmcgm.z01_cgccpf,
                    ed01_c_descr, regimecgm.rh30_descr, ed75_i_saidaescola, cgmrh.z01_telef,
                    celular,ed30_i_ramal, rechumanopessoal.ed284_i_rhpessoal, rechumanocgm.ed285_i_cgm,
                    cgmrh.z01_nome, cgmrh.z01_cgccpf, regimerh.rh30_descr
               order by ed01_c_descr,z01_nome asc  ";

            $result_diretores = db_query($sql);

            if (pg_num_rows($result_diretores) > 0) {
                $this->SetFont('Arial', 'B', 8);
                if ($this->getAvailHeight() <= 8) {
                    $this->AddPage();
                }
                $this->Cell(46, 4, 'CARGO');
                $this->Cell(100, 4, 'NOME / NOME SOCIAL', 0);
                $this->Cell(44, 4, 'CELULAR', 0, 1);
            }

            while ($row_diretores = pg_fetch_array($result_diretores)) {
                $nome = $row_diretores['z04_nomesocial'] == ""
                        ? mb_strimwidth($row_diretores['z01_nome'], 0, 60)
                        : mb_strimwidth($row_diretores['z01_nome'] . " / " . $row_diretores['z04_nomesocial'], 0, 55);
                $cargo = $row_diretores['ed01_c_descr'];
                $telefone = $row_diretores['z01_telef'];
                $ddd = $row_diretores['ddd'];

                $celular = $row_diretores['celular'];

                if ($celular == 0) {
                    $celular = "";
                }

                if ($ddd != "") {
                    if (strlen($ddd) > 2) {
                        $pos = strpos($ddd, ',');
                        if ($pos > 0) {
                            $ddd1 = trim(substr($ddd, 0, $pos));
                            $ddd2 = trim(substr($ddd, $pos + 1));
                        }

                        $ddd1 = $ddd1;
                        $ddd2 = $ddd2;
                    } else {
                        $ddd = $ddd;
                    }
                }

                if ($celular != "") {
                    if (strlen($celular) > 13) {
                        $pos = strpos($celular, ',');
                        if ($pos > 0) {
                            $celular1 = trim(substr($celular, 0, $pos));
                            $celular2 = trim(substr($celular, $pos + 1));
                        }
                        $celular1 = $this->maskFoneDdd($celular1, $ddd1);
                        $celular2 = $this->maskFoneDdd($celular2, $ddd2);

                        $celular = $celular1 . ", " . $celular2;
                    } else {
                        $celular = $this->maskFoneDdd($celular, $ddd);
                    }
                }

                $this->SetFont('Arial', '', 8);

                $this->Cell(46, 4, $cargo);
                $this->Cell(100, 4, $nome);
                $this->Cell(44, 4, $this->maskFoneDdd($celular, $ddd), 0, 1);
            }

            $this->SetY($this->GetY() + 4);
            $this->Line($this->GetX(), $this->GetY(), $this->GetX() + 192, $this->GetY());
            $this->SetY($this->GetY() + 4);
        }
        return $html;
    }

    /**
     * @param $telefone
     * @param $ddd
     * @return mixed|string
     */
    private function maskFoneDdd($telefone, $ddd)
    {
        $tamanho = strlen($telefone);
        if (strlen($ddd) == 0) {
            $ddd = "(00)";
        } else {
            $ddd = "(" . $ddd . ")";
        }

        if ($tamanho == 0 || $telefone == "") {
            return "";
        } else {
            if ($tamanho == 8) {
                $telefone = $ddd . " " . substr($telefone, 0, 4) . "-" . substr($telefone, 4);
            }

            if ($tamanho == 9) {
                $telefone = $ddd . " " . substr($telefone, 0, 5) . "-" . substr($telefone, 5);
            }

            if ($tamanho == 10) {
                $telefone2 = substr($telefone, 2);
                $telefone = substr($telefone, 0, 2) . " " . substr($telefone2, 0, 4) . "-" . substr($telefone2, 4);
            }

            if ($tamanho == 11) {
                $telefone2 = substr($telefone, 2);
                $telefone = substr($telefone, 0, 2) . " " . substr($telefone2, 0, 5) . "-" . substr($telefone2, 5);
            }
        }

        return $telefone;
    }

    private function getQuantidadeAlunosEscola($codigo_escola, $codigos_calendarios)
    {
        $ano_atual = date('Y');

        $sql = "SELECT count(*) as qtd
            FROM matricula
            INNER JOIN turma       ON ed57_i_codigo = ed60_i_turma
            INNER JOIN calendario  ON ed52_i_codigo = ed57_i_calendario
            WHERE ed60_c_situacao = 'MATRICULADO'
            AND extract(year FROM ed60_d_datamatricula) = $ano_atual
            AND ed57_i_escola = $codigo_escola
            AND ed52_c_descr  IN ('$codigos_calendarios') AND ed52_i_ano = $ano_atual ";

        $rs = db_query($sql);

        $qtd_alunos = 0;

        while ($row_escola = pg_fetch_array($rs)) {
            $qtd_alunos = $row_escola['qtd'];
        }

        return $qtd_alunos;
    }
}
