<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */

namespace yamlt;

class YAMLTranslationAdministration {

    public function displayAdminPage() {
        $repository = YAMLTranslationRepository::getInstance();
        $translations = $repository->getAllTranslations();
        $defaultLocale = YAMLTranslationRepository::DEFAULT_LOCALE;

        include dirname(__DIR__) . '/templates/admin_options.php';
    }

}