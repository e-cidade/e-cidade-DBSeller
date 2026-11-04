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

/**
 * Repositoy para os alunos
 * @package   Educacao
 * @author    Andrio Costa - andrio.costa@dbseller.com.br
 * @version   $Revision: 1.13 $
 */
class AlunoRepository {

  private $aAluno = array();
  private static $oInstance;

  private function __construct() {

  }
  private function __clone() {

  }

  /**
   * Retorna a instancia do Repositorio
   * @return AlunoRepository
   */
  protected static function getInstance() {

    if(self::$oInstance == null) {
      self::$oInstance = new AlunoRepository();
    }
    return self::$oInstance;
  }

  /**
   * Verifica se o aluno possui instancia, se não instancia e retorna a instancia de Aluno
   * @param integer $iCodigoAluno
   * @return Aluno
   */
  public static function getAlunoByCodigo($iCodigoAluno) {

    if (!array_key_exists($iCodigoAluno, AlunoRepository::getInstance()->aAluno)) {
      AlunoRepository::getInstance()->aAluno[$iCodigoAluno] = new Aluno($iCodigoAluno);
    }

    return AlunoRepository::getInstance()->aAluno[$iCodigoAluno];
  }

  /**
   * Busca o aluno pela matricula
   * @param integer $iMatricula
   * @return Aluno
   */
  public static function getAlunoByMatricula($iMatricula) {

    $oDaoMatricula = db_utils::getDao('matricula');
    $sSqlMatricula = $oDaoMatricula->sql_query_file($iMatricula, "ed60_i_aluno");
    $rsMatricula   = $oDaoMatricula->sql_record($sSqlMatricula);

    if ($oDaoMatricula->numrows > 0) {
      return AlunoRepository::getInstance()->getAlunoByCodigo(db_utils::fieldsMemory($rsMatricula, 0)->ed60_i_aluno);
    }
    return false;
  }

  /**
   * Busca o aluno pelo cpf
   * @param integer $iMatricula
   * @return Aluno
   */
  public static function getAlunoByCpf($cpf) {

    if (empty($cpf) || is_null($cpf)) {
      return new Aluno();
    }

    $oAluno = null;
    $oDaoAluno = new cl_aluno();
    $sWhereAluno = "ed47_v_cpf = '{$cpf}'";
    $sql = $oDaoAluno->sql_query_file( null, "ed47_i_codigo", null, $sWhereAluno );
    $rsAluno = db_query($sql);
    if ( pg_num_rows( $rsAluno ) > 0 ) {
      $iCodigoAluno = pg_fetch_assoc($rsAluno);
      $oAluno = AlunoRepository::getAlunoByCodigo($iCodigoAluno['ed47_i_codigo']);
    }
    return $oAluno;
  }


    public static function getAlunoByRNM($rnm) {

        if (empty($rnm) || is_null($rnm)) {
            return new Aluno();
        }

        $oAluno = null;
        $oDaoAluno = new cl_aluno();
        $sWhereAluno = "ed47_rnm = '{$rnm}'";
        $sql = $oDaoAluno->sql_query_file( null, "ed47_i_codigo", null, $sWhereAluno );
        $rsAluno = db_query($sql);
        if ( pg_num_rows( $rsAluno ) > 0 ) {
            $iCodigoAluno = pg_fetch_assoc($rsAluno);
            $oAluno = AlunoRepository::getAlunoByCodigo($iCodigoAluno['ed47_i_codigo']);
        }
        return $oAluno;
    }

    /**
     * @param Turma $turma
     * @param array $ordem
     * @return array
     * @throws Exception
     */
    public static function getAlunosByTurma(Turma $turma, array $ordem = array('ed60_i_numaluno', 'ed47_v_nome'))
    {
        $dao = new cl_matricula();
        $campos = array('ed60_i_aluno', 'ed60_i_numaluno');
        $where = array("ed60_i_turma = {$turma->getCodigo()}");
        $sql = $dao->sql_query_aluno_matricula(
            null,
            implode(', ', $campos),
            implode(', ', $ordem),
            implode(' AND ', $where)
        );
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar os alunos da turma {$turma->getDescricao()}. Contate o suporte.");
        }

        $alunos = array();

        if (pg_num_rows($rs) === 0) {
            return $alunos;
        }

        while ($aluno = pg_fetch_object($rs)) {
            $alunos[] = AlunoRepository::getAlunoByCodigo($aluno->ed60_i_aluno);
        }

        return $alunos;
    }

    /**
     * @param Turma $turma
     * @return array
     * @throws Exception
     */
    public static function getAlunosByTurmaOrdemAlfabetica(Turma $turma)
    {
        return static::getAlunosByTurma($turma, array('ed47_v_nome', 'ed60_i_numaluno'));
    }

  /**
   * Adiciona um Aluno ao repositorio
   * @param Aluno $oAluno
   * @return boolean
   */
  public static function adicionarAluno(Aluno $oAluno) {

    if(!array_key_exists($oAluno->getCodigoAluno(), AlunoRepository::getInstance()->aAluno)) {
      AlunoRepository::getInstance()->aAluno[$oAluno->getCodigoAluno()] = $oAluno;
    }
    return true;
  }

  /**
   * Remove um aluno do repository
   * @param Aluno $oAluno
   * @return boolean
   */
  public static function removerAluno(Aluno $oAluno) {

    if (array_key_exists($oAluno->getCodigoAluno(), AlunoRepository::getInstance()->aAluno)) {
      unset(AlunoRepository::getInstance()->aAluno[$oAluno->getCodigoAluno()]);
    }
    return true;
  }

  /**
   * Retorna uma instancia de Aluno encontrado de acordo com os dados recebidos por parâmetro
   * @param string $sNomeAluno
   * @param string $sNomeMae
   * @param DBDate $oDataNascimento
   *
   * @return Aluno $oAluno
   */
  public static function getAlunoPorNomeDataNascimentoNomeMae( $sNomeAluno, $sNomeMae, DBDate $oDataNascimento ) {

    $oAluno       = null;
    $oDaoAluno    = new cl_aluno();
    $sWhereAluno  = "     ed47_v_nome = '{$sNomeAluno}' AND ed47_v_mae = '{$sNomeMae}'";
    $sWhereAluno .= " AND ed47_d_nasc = '{$oDataNascimento->getDate()}' ";
    $sSqlAluno    = $oDaoAluno->sql_query_file( null, "ed47_i_codigo", null, $sWhereAluno );
    $rsAluno      = db_query( $sSqlAluno );

    if ( pg_num_rows( $rsAluno ) > 0 ) {

      $iCodigoAluno = db_utils::fieldsMemory( $rsAluno, 0 )->ed47_i_codigo;
      $oAluno       = AlunoRepository::getAlunoByCodigo( $iCodigoAluno );
    }

    return $oAluno;
  }
}
