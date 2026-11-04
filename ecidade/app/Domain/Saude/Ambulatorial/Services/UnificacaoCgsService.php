<?php

namespace App\Domain\Saude\Ambulatorial\Services;

use App\Domain\Saude\Ambulatorial\Repositories\UnificacaoCgsRepository;

class UnificacaoCgsService
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new UnificacaoCgsRepository();
    }

    public function salvar($dados)
    {
        $cgsCorreto = $this->repository->salvarCgsCorreto($dados);
        if (strlen($cgsCorreto) > 0) {
            foreach ($dados['errados'] as $errado) {
                if (!$this->repository->salvarCgsErrado($errado, $cgsCorreto)) {
                    return false;
                }
            }
        }
        return false;
    }

    public function listarUnificacoes($dados)
    {
        return $this->repository->listarUnificacoes($dados);
    }
    
    public function listarCgs($dados)
    {
        return $this->repository->listarCgs($dados);
    }

    public function detalhar($dados)
    {
        $detalhesUnificacao = $this->repository->detalharUnificacao($dados);
        $cartoesUnificacao = $this->repository->detalharCartoesUnificacao($dados);
        
        return (object)[
            'detalhesUnificacao' => $detalhesUnificacao,
            'cartoesUnificacao' => $cartoesUnificacao
        ];
    }


    public function excluir($dados)
    {
        if ($this->repository->excluirCgsErradoUnificacao($dados)) {
            $this->repository->excluirCgsCorretoUnificacao($dados);
            return true;
        }
        return false;
    }

    public function processar($dados)
    {
        $phpVersion = explode(".", phpversion());
        $executavelPhp = "php".$phpVersion[0].".".$phpVersion[1];
        $codUnificacao = $dados['codigo_unificacao'];
        $loginProcessamento = db_getsession('DB_id_usuario');
        $scriptExecutar = "$executavelPhp FrontIntegracaoExterna.php  --executable duploscgs.php ";
        $scriptExecutar .= " commit $codUnificacao $loginProcessamento 2>&1";
        $output = shell_exec($scriptExecutar);
        
        $cgsCorretos = $this->repository->listarUnificacaoPorCgs($dados);
        foreach ($cgsCorretos as $cgsCorreto) {
            if (!$cgsCorreto->s127_b_proc) {
                throw new \Exception($output);
            }
        }
        return $output;
    }

    public function verificaPermissaoUsuario($permissao)
    {
        $dados = [
            'id_item' => $permissao
        ];
        $permissao = $this->repository->verificaPermissaoUsuario($dados);
        foreach ($permissao as $permissaoInfo) {
            return true;
        }
        return false;
    }
}
