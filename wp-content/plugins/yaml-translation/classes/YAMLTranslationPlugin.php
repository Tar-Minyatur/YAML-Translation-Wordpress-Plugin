<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */

namespace yamlt;

require 'YAMLTranslationRepository.php';
require 'YAMLTranslator.php';

class YAMLTranslatorPlugin {

    /** @var YAMLTranslator */
    private $translator;

    public function __construct() {
        $this->translator = new YAMLTranslator();
    }

    public static function register() {
        $plugin = new YAMLTranslatorPlugin();

        $plugin->registerTranslator();
        $plugin->registerShortcodes();
        $plugin->registerAdministration();
        $plugin->registerLocaleFilter();
    }

    private function registerTranslator() {
        add_filter('gettext', function ($translation, $text, $domain = 'default') {
            if ($domain == 'yaml') {
                return $this->translator->translate($text);
            } else {
                return $translation;
            }
        }, 20, 3);
    }

    private function registerAdministration() {
        if (is_admin()) {
            require 'YAMLTranslationAdministration.php';

            $admin = new YAMLTranslationAdministration();

            add_action('admin_menu', function () use ($admin) {
                add_options_page(
                    'YAML Translation',
                    'YAML Translation',
                    'manage_options',
                    'yaml-translation.php',
                    function () use ($admin) {
                        $admin->displayAdminPage();
                    },
                    20);
            });

            add_filter('enqueue_block_editor_assets', function () {
                wp_enqueue_script('yamlt_block_editor_plugin.js');
            }, 0, 10);

            add_action( 'init', function () {
                wp_register_script(
                    'yamlt_block_editor_plugin.js',
                    plugins_url('js/yamlt_block_editor_plugin.js', dirname(__FILE__)),
                    ['wp-blocks', 'wp-editor', 'wp-element', 'wp-rich-text']
                );
            });
        }
    }

    private function registerShortcodes() {
        add_shortcode('textblock', function ($attributes = [], $content = null, $tag = '') {
            return $this->translator->insertTextBlock($attributes, $content, $tag);
        });
        add_shortcode('set_translation', function ($attributes = [], $content = null, $tag = '') {
            $file = null;
            if (!empty($content)) {
                $file = $content;
            } else if (array_key_exists('file', $attributes)) {
                $file = $attributes['file'];
            }

            if (!is_null($file)) {
                $this->translator->setDefaultFile($file);
            }
            return '';
        });
    }

    private function registerLocaleFilter() {
        add_filter('locale', function ($locale) {
            if (array_key_exists('locale', $_GET)) {
                $locale = $_GET['locale'];
            }
            return $locale;
        }, 1, 1);
    }

}