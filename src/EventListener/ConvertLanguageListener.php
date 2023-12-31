<?php

namespace App\EventListener;

use App\GoogleTranslateClient;
use Exception;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Log\ApplicationLogger;
use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\Advisory;
use Pimcore\Model\DataObject\Category;
use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Crop;
use Pimcore\Model\DataObject\Problem;
use Pimcore\Model\DataObject\Product;
use Pimcore\Model\DataObject\Stage;
use Pimcore\Model\DataObject\State;
use Pimcore\Tool;

class ConvertLanguageListener
{
    public function onPreAddAndUpdate(DataObjectEvent $e)
    {
        $object = $e->getObject();
        if (
            $object  instanceof Product  ||
            $object  instanceof Advisory ||
            $object  instanceof Stage    ||
            $object  instanceof State    ||
            $object  instanceof Category ||
            $object  instanceof Crop     ||
            $object  instanceof Problem
        
        ) {
            $versions = $object->getVersions();
            $priviousObject = null;
            if ($versions && count($versions) > 1) {
                $priviousObject = $versions[count($versions) - 1]->getData();
            }
            $allValidLang = Tool::getValidLanguages();
            $defaultLang = Tool::getDefaultLanguage();
            $classDefinition = ClassDefinition::getById($object->getClassId());
            $localizedfields = $classDefinition->getFieldDefinitions()["localizedfields"];
            try {
                foreach ($localizedfields->getChildren() as $fieldLang) {
                    foreach ($allValidLang as $validLang) {
                        if ($validLang != $defaultLang) {
                            if (!$priviousObject || $priviousObject->get($fieldLang->getname(), $defaultLang) != $object->get($fieldLang->getname(), $defaultLang)) {
                                $reponse = GoogleTranslateClient::getInstance()->getTranslate($object->get($fieldLang->getname(), $defaultLang), $validLang, $defaultLang);
                                $reponse = json_decode($reponse, true);
                                $object->set($fieldLang->getname(), $reponse['data']['translations'][0]['translatedText'], $validLang);
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $logger = ApplicationLogger::getInstance();
                $logger->error($e->getMessage());
            }
        }
    }
}
