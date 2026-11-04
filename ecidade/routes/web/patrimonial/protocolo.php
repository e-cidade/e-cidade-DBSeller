<?php

use App\Domain\Patrimonial\Protocolo\Controller\Processo\ProcessoController;
use App\Domain\Patrimonial\Protocolo\Controller\Processo\ProtocoloDocumentoController;
use App\Domain\Patrimonial\Protocolo\Model\ProtParam;
use Illuminate\Support\Facades\Route;
use \Illuminate\Support\Facades\Request;

Route::get(
    "/capa-preview",
    "\\" .
    ProtocoloDocumentoController::class
    . "@layoutCapaPreview"
);
