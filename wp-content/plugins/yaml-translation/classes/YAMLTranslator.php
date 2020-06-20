<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */

namespace yamlt;

class YAMLTranslator {

    /** @var YAMLTranslationCache **/
    private $cache;

    public function __construct() {
        $this->cache = YAMLTranslationCache::getInstance();
    }

    public function translate($input) {
        $locale = get_locale();
        echo "<pre>Translating to {$locale}: $input</pre>";
        return $this->cache->get($input, $input);
    }

}