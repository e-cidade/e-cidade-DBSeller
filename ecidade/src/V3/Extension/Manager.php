<?php

namespace ECidade\V3\Extension;

use Exception;
use PharData;
use \ECidade\V3\Config\Data as ConfigData;
use \ECidade\V3\Extension\AbstractManager;
use \ECidade\V3\Extension\Data as ExtensionData;
use \ECidade\V3\Extension\Parse\Manifest as ManifestParse;
use \ECidade\V3\Modification\Manager as ModificationManager;

/**
 * @package extension
 */
class Manager extends AbstractManager
{
    /**
     * @param string $id
     * @return boolean
     */
    public static function isEnabled($id)
    {
        return ExtensionData::restore($id)->isEnabled();
    }

    /**
     * @param string $file
     * @param boolean $force
     * @return ExtensionData
     */
    public function unpack($file, $force = false)
    {
        if (!file_exists($file)) {
            throw new Exception("Arquivo não encontrado: $file");
        }

        if (pathinfo($file, PATHINFO_EXTENSION) != "gz") {
            throw new Exception("Arquivo com extensão inválida, esperado tar.gz");
        }

        $pharData = new PharData($file);
        $id = $pharData->getFileName();
        $extensionData = ExtensionData::restore($id);

        if ($extensionData->exists() && !$force) {
            throw new Exception("Extensão já descompactada.");
        }

        if (!isset($pharData[$id . '/Manifest.xml'])) {
            throw new Exception("Arquivo manifest não encontrado no package.");
        }

        if (!$pharData->extractTo(ECIDADE_EXTENSION_PACKAGE_PATH, null, true)) {
            throw new Exception("Erro ao descompactar arquivo: $file");
        }

        $parse = $this->parse($id);

        if (!$extensionData->exists()) {
            $extensionData->setStatus(ExtensionData::STATUS_DISABLED);
            $extensionData->setId($parse->getId());
            $extensionData->setVersion($parse->getVersion());
            $extensionData->setType($parse->getType());
            $extensionData->setModifications($parse->getModifications());
            $extensionData->setEvents($parse->getEvents());
            $extensionData->setManager($parse->getManager());
            $extensionData->save();
        }

        if ($extensionData->hasManager()) {
            $extensionManager = $extensionData->getManager();
            $extensionManager = new $extensionManager($this->container);
            $extensionManager->unpack();
        }

        return $extensionData;
    }

    /**
     * @param string $id
     * @return \ECidade\Extension\Parse\Manifest
     */
    public function parse($id)
    {
        $path = ECIDADE_EXTENSION_PACKAGE_PATH . $id . '/Manifest.xml';

        // parse no xml
        $parse = new ManifestParse($path);
        return $parse->load()->parse();
    }

    /**
     * @param integer $id
     * @return boolean
     */
    public function install($id)
    {
        $this->container->get('logger')->debug('Instalando ' . $id);
        $this->container->get('logger')->debug('install(' . $id . ')');

        $extensionData = ExtensionData::restore($id);
        $configData = ConfigData::restore('config');

        if (!$extensionData->exists()) {
            throw new Exception("Extensão não descompactada: '$id'");
        }

        if ($extensionData->isEnabled()) {
            throw new Exception("Extensão já instalada: '$id'");
        }

        if ($extensionData->hasModifications()) {
            $modificationManager = new ModificationManager($this->container);
            $modificationManager->install($extensionData->getModifications());
        }

        if ($extensionData->hasEvents()) {
            foreach ($extensionData->getEvents() as $trigger => $events) {
                foreach ($events as $event) {
                    $configData->addEvent($event, $trigger);
                }
            }
        }

        $extensionData->setStatus(ExtensionData::STATUS_ENABLED);

        if ($extensionData->hasManager()) {
            $extensionManager = $extensionData->getManager();
            $extensionManager = new $extensionManager($this->container);
            $extensionManager->install($extensionData, null);
        }

        $extensionData->save();
        $configData->save();

        return $extensionData->getStatus() === ExtensionData::STATUS_ENABLED;
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function uninstall($id)
    {
        $this->container->get('logger')->debug('Desinstalando ' . $id);

        $configData = ConfigData::restore('config');
        $extensionData = Data::restore($id);

        if (false === $extensionData->exists()) {
            throw new Exception("Extensão não instalada: $id");
        }

        if (false === $extensionData->isEnabled()) {
            throw new Exception("Extensão desativada: $id");
        }

        if ($extensionData->hasModifications()) {
            $modificationManager = new ModificationManager($this->container);
            $modificationManager->uninstall($extensionData->getModifications());
        }

        if ($extensionData->hasEvents()) {
            foreach ($extensionData->getEvents() as $trigger => $events) {
                foreach ($events as $event) {
                    $configData->removeEvent($event);
                }
            }
        }

        $extensionData->setStatus(ExtensionData::STATUS_DISABLED);

        if ($extensionData->hasManager()) {
            $extensionManager = $extensionData->getManager();
            $extensionManager = new $extensionManager($this->container);
            $extensionManager->uninstall($extensionData);
        }

        $extensionData->save();
        $configData->save();

        return true;
    }
}
