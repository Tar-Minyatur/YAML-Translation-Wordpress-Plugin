<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>PO Converter</title>
    <style type="text/css">
        body { font-family: system-ui, sans-serif; font-size: 16px; }
        .tall { height: 30rem; }
        .full-width { width: 100%; }
        textarea.full-width { white-space: pre; }
        .message { border: 1px #777700 solid; padding: 0.2rem; color: #777700; background: #cccc88; margin-bottom: 0.5rem; }
        .error { border-color: #770000; color: #770000; background: #cc8888; }
        .side-by-side { display: flex; flex-direction: row; width: 100%; flex-wrap: nowrap; justify-content: space-between; }
        .side-by-side > div { flex-basis: auto; flex-grow: 1; margin-left: 1rem; }
        .side-by-side > div:first-child { margin-left: 0; }
    </style>
</head>
<body>
<div class="wrapper">
    <h1>PO Converter</h1>
    <p>
        This tool extracts any text from an HTML, converts inline formatting to Markdown
        and renders out a .po file.
    </p>
    <?php
    // TODO Separate logic and view if this will actually be used
    class HTMLTextExtractor {

        private $texts = [];
        private $html = null;

        // TODO Make these configurable through the constructor or setters
        private $allowedFormattingTags = ['b', 'strong', 'i', 'em', 'u', 's', 'a'];

        public function convert(string $html): bool {
            $this->texts = [];
            $parsed = new DOMDocument();
            if ($parsed->loadHTML($html) === false) {
                return false;
            }
            $parsed->encoding = 'UTF-8';
            $this->html = $this->replaceTexts($parsed);
            return true;
        }

        public function getHTML(): string {
            return is_null($this->html) ? '' : $this->html->saveHTML();
        }

        public function getTexts(): array {
            return $this->texts;
        }

        public function lastError(): string {
            $error = libxml_get_last_error();
            return (!$error) ? '' : $error->message;
        }

        private function replaceTexts(DOMNode $document, $inBody = false) {
            foreach ($document->childNodes as $node) {
                if (in_array($node->nodeName, ['script', 'style', 'meta', '#comment'])) {
                    continue;
                }
                if ($node->hasChildNodes()) {
                    $formattedText = $this->convertNodeToFormattedText($node);
                    if (!is_null($formattedText)) {
                        foreach ($node->childNodes as $n) { $node->removeChild($n); }
                        $node->textContent = $formattedText;
                        $this->convertTextNode($node);
                    } else {
                        $this->replaceTexts($node, ($node->nodeName == 'body') || $inBody);
                    }
                } else if ($inBody) {
                    $this->convertTextNode($node);
                }
            }
            return $document;
        }

        private function convertTextNode(DOMNode $node) {
            $text = trim($node->textContent ?? '');
            if (!empty($text)) {
                $newText = utf8_decode(preg_replace('#\s+#m', ' ', $text));
                $this->texts[] = $newText;
                $node->textContent = $newText;
            }
        }

        private function convertNodeToFormattedText(DOMNode $node) {
            $text = '';
            foreach ($node->childNodes as $node) {
                if (!in_array($node->nodeName, $this->allowedFormattingTags) && ($node->nodeName !== '#text')) {
                    return null;
                }
                $text .= $this->nodeToMarkdown($node);
            }
            return empty($text) ? null : $text;
        }

        private function nodeToMarkdown(DOMNode $node) {
            if (in_array($node->nodeName, ['strong', 'b'])) {
                $pattern = '**%s**';
            } else if (in_array($node->nodeName, ['em', 'i'])) {
                $pattern = '*%s*';
            } else if ($node->nodeName == 's') {
                $pattern = '~~%s~~';
            } else if ($node->nodeName == 'u') {
                $pattern = '_%s_';
            } else if ($node->nodeName == 'a') {
                $pattern = '[%s](' . ($node->attributes->getNamedItem('href')->nodeValue ?? '') . ')';
            } else if ($node->nodeName == '#text') {
                $pattern = '%s';
            } else {
                return null;
            }
            $textContent = '';
            if ($node->hasChildNodes()) {
                foreach ($node->childNodes as $n) {
                    $text = $this->nodeToMarkdown($n);
                    if (is_null($text)) {
                        return null;
                    }
                    $textContent .= $text;
                }
            } else {
                $textContent = $node->nodeValue;
            }
            return sprintf($pattern, $textContent);
        }

    }

    if (array_key_exists('html', $_POST)) {
        $converter = new HTMLTextExtractor();

        if (!$converter->convert($_POST['html'])) {
            $error = 'Could not parse the input file. Make sure it is a full HTML website without any syntax errors. Details: ' . $converter->lastError();
        } else {
            $texts = $converter->getTexts();
            $html = $converter->getHTML();
            $pofile = '';
            foreach ($texts as $key => $text) {
                $pofile .= sprintf("msgid \"%s\"\nmsgstr \"\"\n\n", addcslashes($text, '"'));
            }

            $message = 'Found '.count($texts).' text block(s) to extract from the HTML:';
        }
    }
    ?>
    <?php if (isset($error)): ?>
        <div class="error message"><?= htmlentities($error) ?></div>
    <?php endif ?>
    <?php if (isset($message)): ?>
        <div class="message"><?= htmlentities($message) ?></div>
    <?php endif ?>

    <?php if (isset($texts) && isset($html)):  ?>
        <div class="side-by-side">
            <div>
                <h2>PO file:</h2>
                <textarea class="full-width tall"><?= $pofile ?></textarea>
            </div>
            <div>
                <h2>HTML (with placeholders):</h2>
                <textarea class="full-width tall"><?= htmlentities($html) ?></textarea>
            </div>
        </div>
        <p>Want to try another one? <a href="<?= $_SERVER['PHP_SELF'] ?>">This way...</a></p>
    <?php else: ?>
        <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="post">
            <textarea class="full-width tall" name="html">
&lt;!doctype html&gt;
&lt;html&gt;
&lt;body&gt;
&lt;div&gt;
   &lt;p&gt;Test&lt;/p&gt;
   &lt;p&gt;Slightly longer text&lt;/p&gt;
   &lt;p&gt;Significantly longer text block that should get a shortened key.&lt;/p&gt;
   &lt;p&gt;Some text with a single &lt;strong&gt;bold&lt;/strong&gt; word.&lt;/p&gt;
   &lt;p&gt;This is a text block with &lt;strong&gt;bold text&lt;/strong&gt;, &lt;em&gt;italics&lt;/em&gt; and a &lt;a href="https://tshw.de"&gt;link&lt;/a&gt;.&lt;/p&gt;
   &lt;p&gt;This block should be &lt;div&gt;split&lt;/div&gt; apart!&lt;/p&gt;
&lt;/div&gt;
&lt;/body&gt;
&lt;/html&gt;
            </textarea>
            <button type="submit">Convert!</button>
        </form>
    <?php endif ?>
</div>
</body>
</html>
