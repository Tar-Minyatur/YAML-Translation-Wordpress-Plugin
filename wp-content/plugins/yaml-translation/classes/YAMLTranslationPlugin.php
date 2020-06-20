<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */

namespace yamlt;

require 'YAMLTranslationCache.php';
require 'YAMLTranslator.php';

class YAMLTranslatorPlugin {

    public static function register() {
        $plugin = new YAMLTranslatorPlugin();

        $plugin->registerTranslator();
        $plugin->registerAdministration();
    }

    private function registerTranslator() {
        $translator = new YAMLTranslator();
        add_filter('gettext', function ($translation, $text, $domain = 'default') use ($translator) {
            if ($domain == 'yaml') {
                return $translator->translate($text);
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
        }
    }

}