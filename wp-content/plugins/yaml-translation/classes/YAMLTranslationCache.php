<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */

namespace yamlt;

class YAMLTranslationCache {

    /** @var YAMLTranslationCache **/
    private static $instance = null;

    private $cache = [
        'Let\'s have some fun' => 'Lass uns etwas Spaß haben'
    ];

    private function __construct() {
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new YAMLTranslationCache();
        }
        return self::$instance;
    }

    public function get($key, $default = null) {
        return array_key_exists($key, $this->cache) ? $this->cache[$key] : $default;
    }

}