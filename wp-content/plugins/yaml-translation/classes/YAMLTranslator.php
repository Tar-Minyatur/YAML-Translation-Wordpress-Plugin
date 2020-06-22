<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */

namespace yamlt;

class YAMLTranslator {

    /** @var YAMLTranslationRepository **/
    private $repository;

    /** @var string */
    private $defaultFile = 'home';

    public function __construct() {
        $this->repository = YAMLTranslationRepository::getInstance();
    }

    public function translate($input) {
        $locale = get_locale();
        return $this->repository->get($locale, $this->defaultFile, $input, $input);
    }

    public function setDefaultFile(string $file) {
        $this->defaultFile = $file;
    }

    public function insertTextBlock($attributes = [], $content = null, $tag = '') {
        $locale = get_locale();

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
            return nl2br(esc_html($this->repository->get($locale, $file, $key, true)));
        } else {
            return '[Text block definition incomplete. Cannot find translation. ☹]';
        }
    }

}