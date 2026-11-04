<?php

namespace App\Domain\Tributario\ISSQN\Reports\Alvara;

use App\Domain\Tributario\ISSQN\Interfaces\Reports\AlvaraInterface;
use App\Domain\Tributario\ISSQN\Model\Base\IssBase;
use db_stdClass;
use Illuminate\Support\Facades\DB;

class AgataDocumentTemplateAlvara extends AlvaraBaseReport implements AlvaraInterface
{
    const TEMPLATE_FILE_PATH = "issqn/alvara.agt";

    private $hasDatabaseTransaction;

    public function __construct(IssBase $issBase, $hasDatabaseTransaction)
    {
        parent::__construct($issBase);
        $this->hasDatabaseTransaction = $hasDatabaseTransaction;
    }

    /**
     * @throws \Exception
     */
    public function generate()
    {
        require_once(modification("libs/db_libsys.php"));
        require_once(modification("dbagata/classes/core/AgataAPI.class"));

        db_putsession("DB_user", env('DB_USERNAME'));
        db_putsession("DB_senha", env('DB_PASSWORD'));
        db_putsession("DB_servidor", env('DB_HOST'));
        db_putsession("DB_porta", env('DB_PORT'));

        $documentCode = $this->getDocumentCode();
        $documentType = 6;

        $sxwFilePath = $this->getFullFilePath("sxw");

        $param = ['$inscr' => $this->issBase->q02_inscr];

        error_reporting(0);
        $generated = db_stdClass::oo2pdf(
            $documentType,
            $documentCode,
            AgataDocumentTemplateAlvara::TEMPLATE_FILE_PATH,
            $param,
            $sxwFilePath,
            null,
            false,
            false,
            $this->hasDatabaseTransaction
        );
        error_reporting(1);

        if (!$generated) {
            throw new \Exception("Não foi possível gerar o alvará");
        }
    }

    /**
     * @throws \Exception
     */
    private function getDocumentCode()
    {
        $alvaraType = IssBase::join("issalvara", "q123_inscr", "q02_inscr")
                             ->join("isstipoalvara", "q98_sequencial", "q123_isstipoalvara")
                             ->where("q02_inscr", $this->issBase->q02_inscr)
                             ->first(["q98_documento"]);

        if (!$alvaraType) {
            throw new \Exception("Tipo de alvará não encontrado.");
        }

        return $alvaraType->q98_documento;
    }
}
