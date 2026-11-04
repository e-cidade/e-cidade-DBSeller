<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\NovaLRF;

use App\Domain\Configuracao\Helpers\StorageHelper;
use App\Domain\Financeiro\Contabilidade\Models\EmissoesLrf;
use ECidade\Lib\File\FileEstorage;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EmissaoLrfService
{
    public function getByFilters($filters = [])
    {
        return EmissoesLrf::query()
            ->when(!empty($filters['relatorio']), function (Builder $builder) use ($filters) {
                $builder->where('c181_relatorio', $filters['relatorio']);
            })
            ->when(!empty($filters['periodo']), function (Builder $builder) use ($filters) {
                $builder->where('c181_periodo', $filters['periodo']);
            })
            ->when(!empty($filters['instituicao']), function (Builder $builder) use ($filters) {
                $builder->where('c181_instituicao', $filters['instituicao']);
            })
            ->when(!empty($filters['usuario']), function (Builder $builder) use ($filters) {
                $builder->where('c181_usuario', $filters['usuario']);
            })
            ->when(!empty($filters['usuario']), function (Builder $builder) use ($filters) {
                $builder->where('c181_usuario', $filters['usuario']);
            })
            ->when(!empty($filters['publicado']), function (Builder $builder) use ($filters) {
                $builder->where('c181_publicado', $filters['publicado']);
            })
            ->when(!empty($filters['with']), function (Builder $builder) use ($filters) {
                foreach ($filters['with'] as $with) {
                    $builder->with($with);
                }
            })
            ->get();
    }

    public function publicar($codigo, $publicar)
    {
        $emissao = EmissoesLrf::find($codigo);

        /**
         * Se for para publicar, valida se não tem uma publicação
         */
        if ($publicar) {
            $validar = [
                "relatorio" => $emissao->c181_relatorio,
                "periodo" => $emissao->c181_periodo,
                "instituicao" => $emissao->c181_instituicao,
                "publicado" => 't'
            ];
            /**
             * @var $retorno Collection
             */
            $retorno = $this->getByFilters($validar);
            if (!$retorno->isEmpty()) {
                throw new Exception('Já existe uma publicação para esse período.', 403);
            }
        }

        $emissao->c181_publicado = $publicar;
        $emissao->save();
    }

    public function deletar($codigo)
    {
        $emissao = EmissoesLrf::find($codigo);

        if (!is_null($emissao->c181_storage)) {
            $storage = \JSON::create()->parse($emissao->c181_storage);
            StorageHelper::deleteArquivo($storage->pdf);
            StorageHelper::deleteArquivo($storage->xls);
        }

        $emissao->delete();
        return true;
    }

    public function download($codigo)
    {
        $data = (new FileEstorage())->getPathRelative($codigo);

        if (!file_exists($data)) {
            throw new Exception('Não foi possível recuperar o arquivo. Contate o suporte.');
        }

        return $data;
    }
}
