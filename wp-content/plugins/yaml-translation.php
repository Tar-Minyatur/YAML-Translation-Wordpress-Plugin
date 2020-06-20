<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */
/*
Plugin Name: YAML Translation
Plugin URI: https://github.com/Tar-Minyatur
Description: In development...
Author: Till Helge Helwig
Version: 0.0.1
Author URI: https://twitter.com/TillHelge
*/

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

class YAMLTranslator {

	/** @var YAMLTranslationCache **/
	private $cache;

	public function __construct() {
		$this->cache = YAMLTranslationCache::getInstance();
	}

	public static function register() {
		$translator = new YAMLTranslator();
		add_filter('gettext', function ($translation, $text, $domain = 'default') use ($translator) { 
			if ($domain == 'yaml') {
				return $translator->translate($text);
			} else {
				return $translation;
			}
		}, 20, 3);
	}

	public function translate($input) {
		$locale = get_locale();
		echo "<pre>Translating to {$locale}: $input</pre>";
		return $this->cache->get($input, $input);
	}

}

YAMLTranslator::register();

