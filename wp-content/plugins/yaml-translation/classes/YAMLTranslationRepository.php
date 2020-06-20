<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */

namespace yamlt;

class YAMLTranslationRepository {
    const TRANSLATION_MISSING = '[Translation missing ☹]';

    /** @var YAMLTranslationRepository **/
    private static $instance = null;

    // TODO Make this configurable
    const YAML_FILE_FOLDER = WP_CONTENT_DIR . '/Localization/';

    // TODO Make this configurable
    const DEFAULT_LOCALE = 'en_US';

    /** @var array */
    private $translations = [];

    /** @var string */
    private $defaultLocale;

    private function __construct($defaultLocale) {
        $this->defaultLocale = $defaultLocale;
        $this->loadTranslations();
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new YAMLTranslationRepository(self::DEFAULT_LOCALE);
        }
        return self::$instance;
    }

    private function log($message) {
        // TODO Collect these messages and show them in the admin panel instead of writing them to the system log
        error_log($message);
    }

    public function get($locale, $file, $key, $fallbackToDefault = true) {
        if (isset($this->translations[$locale][$file][$key])) {
            // Translation found
            return $this->translations[$locale][$file][$key];
        } else if ($fallbackToDefault) {
            if (isset($this->translations[$this->defaultLocale][$file][$key])) {
                $this->log("Translation warning: Translation for locale $locale in file $file is missing for key $key");
                // Using fallback default language
                return $this->translations[$this->defaultLocale][$file][$key];
            } else {
                $this->log("Translation error: Could not find any translation for locale $locale or default locale {$this->defaultLocale} in file $file with key $key");
                return self::TRANSLATION_MISSING;
            }
        } else {
            $this->log("Translation error: Could not find any translation for locale $locale in file $file with key $key");
            return self::TRANSLATION_MISSING;
        }
    }

    private function importYamlFiles($path) {
        $translation = [];
        foreach (scandir($path) as $file) {
            $filepath = $path . '/' . $file;
            $extension = pathinfo($filepath, PATHINFO_EXTENSION);
            if ($extension != 'yaml') continue;
            $data = yaml_parse_file($filepath);
            if ($data !== false) {
                $strings = $data;
                if (array_key_exists('text', $data)) {
                    $strings = $data['text'];
                    if (array_key_exists('title', $data)) {
                        $strings['title'] = $data['title'];
                    }
                }
                $translation[basename($file, '.yaml')] = $strings;
            }
        }
        return $translation;
    }

    private function loadTranslations() {
        $translations = [];
        foreach (scandir(self::YAML_FILE_FOLDER) as $directory) {
            if (preg_match('#^[a-z]{2}-[A-Z]{2}$#', $directory)) {
                $locale = str_replace('-', '_', $directory);
                $files = $this->importYamlFiles(self::YAML_FILE_FOLDER . $directory);
                if (count($files) > 0) {
                    $translations[$locale] = $files;
                }
            }
        }
        $this->translations = $translations;
    }

    public function getAllTranslations() {
        return $this->translations;
    }

}