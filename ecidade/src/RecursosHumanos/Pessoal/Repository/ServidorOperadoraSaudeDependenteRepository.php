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

namespace ECidade\RecursosHumanos\Pessoal\Repository;

use cl_servidoroperadorasaudedependente;
use Dependente;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorOperadoraSaude;
use ECidade\RecursosHumanos\Pessoal\Model\ServidorOperadoraSaudeDependente;
use Rubrica;
use Exception;

/**
 * Class ServidorOperadoraSaudeDependenteRepository
 * @package ECidade\RecursosHumanos\Pessoal\Repository
 */
class ServidorOperadoraSaudeDependenteRepository
{
    /**
     * @var array
     */
    private $scopes = array();

    /**
     * @param array|int $ids
     * @return int
     * @throws Exception
     */
    public static function destroy($ids)
    {
        $count = 0;
        $ids = is_array($ids) ? $ids : func_get_args();

        $self = new self();

        foreach ($ids as $id) {
            $self->delete(self::find($id));
            $count++;
        }

        return $count;
    }

    /**
     * @param ServidorOperadoraSaudeDependente|null $servidorOperadoraSaudeDependente
     * @throws Exception
     */
    public function delete(ServidorOperadoraSaudeDependente $servidorOperadoraSaudeDependente = null)
    {
        $id = $servidorOperadoraSaudeDependente instanceof ServidorOperadoraSaudeDependente ?
            $servidorOperadoraSaudeDependente->getSequencial() : null;

        $dao = new cl_servidoroperadorasaudedependente();
        $dao->excluir($id, implode(' AND ', $this->scopes));

        if ($dao->erro_status === '0') {
            throw new Exception("Não foi possível excluir o dependente do servidor " .
            "{$servidorOperadoraSaudeDependente->getServidorOperadoraSaude()->getServidor()->getCgm()->getNome()}.\n" .
            "Contate o suporte.");
        }
    }

    /**
     * @param $id
     * @param array $columns
     * @return bool|ServidorOperadoraSaudeDependente
     * @throws Exception
     */
    public static function find($id, $columns = array('*'))
    {
        $dao = new cl_servidoroperadorasaudedependente();
        $sql = $dao->sql_query($id, implode(', ', $columns));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o dependente do servidor.\nContate o suporte.");
        }

        if (pg_num_rows($rs) === 0) {
            return false;
        }

        $resultado = pg_fetch_array($rs);

        return ServidorOperadoraSaudeDependente::fromState($resultado);
    }

    /**
     * @param array $columns
     * @return ServidorOperadoraSaudeDependente[]
     * @throws Exception
     */
    public function all($columns = array('*'))
    {
        $dao = new cl_servidoroperadorasaudedependente();
        $sql = $dao->sql_query(null, implode(', ', $columns));
        $rs = db_query($sql);

        $servidorOperadorasSaudeDependente = array();

        if (pg_num_rows($rs) === 0) {
            return $servidorOperadorasSaudeDependente;
        }

        while ($servidorOperadoraSaudeDependente = pg_fetch_array($rs)) {
            $servidorOperadorasSaudeDependente[] =
                ServidorOperadoraSaudeDependente::fromState($servidorOperadoraSaudeDependente);
        }

        return $servidorOperadorasSaudeDependente;
    }

    /**
     * @return ServidorOperadoraSaudeDependente[]
     * @throws Exception
     */
    public function get()
    {
        $dao = new cl_servidoroperadorasaudedependente();
        $sql = $dao->sql_query(null, '*', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o dependentes dos servidores.\nContate o suporte.");
        }

        $servidorOperadorasSaudeDependente = array();

        if (pg_num_rows($rs) === 0) {
            return $servidorOperadorasSaudeDependente;
        }

        while ($servidorOperadoraSaudeDependente = pg_fetch_array($rs)) {
            $servidorOperadorasSaudeDependente[] =
                ServidorOperadoraSaudeDependente::fromState($servidorOperadoraSaudeDependente);
        }

        return $servidorOperadorasSaudeDependente;
    }

    /**
     * @param ServidorOperadoraSaudeDependente $servidorOperadoraSaudeDependente
     * @return ServidorOperadoraSaudeDependente
     * @throws Exception
     */
    public function save(ServidorOperadoraSaudeDependente $servidorOperadoraSaudeDependente)
    {
        $dao = new cl_servidoroperadorasaudedependente();
        $dao->rh223_sequencial = $servidorOperadoraSaudeDependente->getSequencial();
        $dao->rh223_tipo = $servidorOperadoraSaudeDependente->getTipo();
        $dao->rh223_valor = $servidorOperadoraSaudeDependente->getValor();
        $dao->rh223_servidoroperadorasaude =
        $servidorOperadoraSaudeDependente->getServidorOperadoraSaude()->getSequencial();
        $dao->rh223_dependente = $servidorOperadoraSaudeDependente->getDependente()->getCodigo();
        $dao->rh223_rubrica = $servidorOperadoraSaudeDependente->getRubrica()->getCodigo();

        if (empty($dao->rh223_sequencial)) {
            $dao->incluir(null);
        } else {
            $dao->alterar($dao->rh223_sequencial);
        }
        if ($dao->erro_status === '0') {
            throw new Exception("Não foi possível salvar as informações.\nContate o suporte.");
        }

        $servidorOperadoraSaudeDependente->setSequencial($dao->rh223_sequencial);
        return $servidorOperadoraSaudeDependente;
    }

    /**
     * @param $tipo
     * @param string $operator
     * @return $this
     */
    public function scopeTipo($tipo, $operator = '=')
    {
        $this->scopes[] = "rh223_tipo {$operator} '{$tipo}'";
        return $this;
    }

    /**
     * @param $valor
     * @param string $operator
     * @return $this
     */
    public function scopeValor($valor, $operator = '=')
    {
        $this->scopes[] = "rh223_valor {$operator} {$valor}";
        return $this;
    }

    /**
     * @param ServidorOperadoraSaude $servidorOperadoraSaude
     * @param string $operator
     * @return $this
     */
    public function scopeServidorOperadoraSaude(ServidorOperadoraSaude $servidorOperadoraSaude, $operator = '=')
    {
        $this->scopes[] = "rh223_servidoroperadorasaude {$operator} {$servidorOperadoraSaude->getSequencial()}";
        return $this;
    }

    /**
     * @param Dependente $dependente
     * @param string $operator
     * @return $this
     */
    public function scopeDependente(Dependente $dependente, $operator = '=')
    {
        $this->scopes[] = "rh223_dependente {$operator} {$dependente->getCodigo()}";
        return $this;
    }

    /**
     * @param $id
     * @param string $operator
     * @return $this
     */
    public function scopeSequencial($id, $operator = '=')
    {
        $this->scopes[] = "rh223_sequencial {$operator} {$id}";
        return $this;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function count()
    {
        $dao = new cl_servidoroperadorasaudedependente();
        $sql = $dao->sql_query(null, 'count(*)', null, implode(' AND ', $this->scopes));
        $rs = db_query($sql);

        if (!$rs) {
            throw new Exception("Não foi possível buscar o dependentes dos servidores.\nContate o suporte.");
        }

        return (int)pg_fetch_result($rs, 0, 'count');
    }

    /**
     * @return $this
     */
    public function resetScopes()
    {
        $this->scopes = array();
        return $this;
    }

    /**
     * @param Rubrica $rubrica
     * @param string $operator
     * @return $this
     */
    public function scopeRubrica(Rubrica $rubrica, $operator = 'ILIKE')
    {

        $this->scopes['rubrica'] = "rh223_rubrica {$operator} '%{$rubrica->getCodigo()}%'";
        return $this;
    }
}
