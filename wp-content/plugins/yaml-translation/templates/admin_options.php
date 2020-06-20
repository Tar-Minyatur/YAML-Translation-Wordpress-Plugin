<?php
/**
 * @package YAML_Translation
 * @version 0.0.1
 */
?>
<div class="wrap">
    <h1>YAML Translation</h1>

    <h2><?= count($translations) ?> Available Translation(s)</h2>
        <?php foreach ($translations as $locale => $translation): ?>
            <h3>
                <?= $locale ?>
                <?php if ($locale == $defaultLocale): ?>
                <em>(default)</em>
                <?php endif ?>
            </h3>
            <?php foreach ($translation as $file => $strings): ?>
            <table class="wp-list-table widefat striped posts">
                <colgroup>
                    <col style="width: 120px; word-wrap: break-word">
                </colgroup>
                <tr>
                    <td rowspan="<?= count($strings) + 1 ?>"><strong><?= $file ?></strong></td>
                    <td colspan="2"><em><?= count($strings) ?> text block(s) found in this file:</em></td>
                </tr>
                <?php foreach ($strings as $key => $string): ?>
                <tr>
                    <td><?= esc_html($key) ?></td>
                    <td>
                        <?php if (!is_string($string)): ?>
                            <pre><?php var_dump($string) ?></pre>
                        <?php else: ?>
                            <div style="white-space: pre-line"><?= esc_html($string) ?></div>
                        <?php endif ?>

                    </td>
                </tr>
                <?php endforeach ?>
            </table>
            <?php endforeach ?>
        <?php endforeach ?>
</div>
