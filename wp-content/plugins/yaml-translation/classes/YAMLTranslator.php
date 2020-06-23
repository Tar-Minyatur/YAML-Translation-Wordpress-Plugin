<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */

namespace yamlt;

require plugin_dir_path(__FILE__) . '../lib/Parsedown.php';

class YAMLTranslator {

    /** @var YAMLTranslationRepository **/
    private $repository;

    /** @var string */
    private $defaultFile = 'home';

    /** @var \Parsedown */
    private $markdownConverter;

    public function __construct() {
        $this->repository = YAMLTranslationRepository::getInstance();
        $this->markdownConverter = new \Parsedown();
    }

    public function translate($input) {
        $locale = get_locale();
        return $this->repository->get($locale, $this->defaultFile, $input, $input);
    }

    public function setDefaultFile(string $file) {
        $this->defaultFile = $file;
    }

    public function insertTextBlock($attributes = [], $content = null, $tag = '') {
        $locale = array_key_exists('locale', $_GET) ? $_GET['locale'] : get_locale();

        $file = $this->defaultFile;
        if (is_array($attributes) && array_key_exists('file', $attributes)) {
            $file = $attributes['file'];
        }

        $key = null;
        if (is_array($attributes) && array_key_exists('id', $attributes)) {
            $key = is_numeric($attributes['id']) ? intval($attributes['id']) : $attributes['id'];
        } else if (!is_null($content)) {
            $key = $content;
        }

        if (!is_null($key)) {
            $string = esc_html($this->repository->get($locale, $file, $key, true));
            $string = $this->markdownConverter->line($string);
            $string = nl2br($string);
            if (array_key_exists('show_translation_keys', $_GET)) {
                $string .= sprintf('<div style="display: inline-block; width: auto; text-transform: none; padding: 1px 3px; letter-spacing: 0; font-weight: normal; font-family: monospace; font-size: 10px; color: #fff; background: #000066; border-radius: 5px;">%s</div>', esc_html($key));
            }
            return $string;
        } else {
            return '[Text block definition incomplete. Cannot find translation. ☹]';
        }
    }

}