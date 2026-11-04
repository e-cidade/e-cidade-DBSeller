<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;

class ClearArquivosInfinity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saude:limparpastabackupinfinity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpa a pasta de backup de pedidos de exame da integracao infinity';

    /**
     * Locais Permitidos
     *
     * @var array<string, string>
     */
    private $locais = [
        'saudeInfinity' => 'storage/app/backupPedidosInfinity'
    ];

    /**
     * Extensões Permitidas
     *
     * @var array<string, string> $allowedExtensions
     */
    private $allowedExtensions = [
        'infinity'     => '.infinity'
    ];

    /**
     * Local padrao para pesquisa e remocao dos arquivos
     *
     * @var string $local
     */
    private $local = 'saudeInfinity';

    /**
     * Dias
     *
     * @var int $days
     */
    private $days = 7;

    /**
     * Extenção dos arquivos de file
     *
     * @var string $extension
     */
    private $extension = 'infinity';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $local     = $this->getLocal();
            $extension = $this->getExtension();
            $days      = $this->getday();
            $this->pathFindFile($local, $days);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Retorna e valida o local
     *
     * @return string
     */
    private function getLocal()
    {
        $local = $this->local;

        if (empty($local)) {
            throw new Exception('Favor informar o local de backup da pasta infinity.');
        }

        if (!array_key_exists($local, $this->locais)) {
            throw new Exception('Caminho da pasta de backup infinity inválido para limpeza.');
        }

        return $this->locais[$local];
    }

    /**
     * Retorna e valida a extension
     *
     * @return string
     */
    private function getExtension()
    {
        $extension = $this->extension;

        if (empty($extension)) {
            $extension = '*';
        }

        if (!array_key_exists($extension, $this->allowedExtensions)) {
            throw new Exception('Extensão inválida. Backup infinity');
        }

        return $this->allowedExtensions[$extension];
    }

    /**
     * Retorna e valida os dias
     *
     * @return int
     */
    private function getday()
    {
        $day = $this->days;

        if (empty($day)) {
            $day = 7;
        }

        if (!is_numeric($day)) {
            throw new Exception('Valor de tipo inválido. Backup infinity');
        }

        if ($day <= 0) {
            throw new Exception('Período de busca inválido. Backup infinity');
        }

        return (int) $day;
    }

    /**
     * @param string $pathBase
     * @param integer $numberDays
     */
    public function pathFindFile($pathBase, $numberDays)
    {
        $path = ECIDADE_PATH . $pathBase;

        if (!file_exists($path)) {
            return;
        }

        $dir  = dir($path);

        while ($pathFileDir = $dir->read()) {
            if (array_search($pathFileDir, ['..', '.', '.gitignore']) !== false) {
                continue;
            }

            $realPathFileDir = ECIDADE_PATH . $pathBase . DIRECTORY_SEPARATOR . $pathFileDir;

            if (is_dir($realPathFileDir)) {
                $this->pathFindFile($pathBase . DIRECTORY_SEPARATOR . $pathFileDir);
            } else {
                $dataArquivo = date_create(date('Y-m-d H:i:s', filemtime($realPathFileDir)));
                $dataHoje = date_create(date('Y-m-d H:i:s'));
                $diasArquivoExiste = $dataArquivo->diff($dataHoje);

                if ($diasArquivoExiste->days >= $numberDays) {
                    $this->deleteFile($pathFileDir, $realPathFileDir);
                }
            }
        }

        $dir->close();
    }

    /**
     * Deleta o arquivo
     *
     * @param string
     * @param string
     *
     */
    public function deleteFile($nameFile, $filePath)
    {
        unlink($filePath);
    }
}
