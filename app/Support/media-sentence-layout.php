<?php
declare(strict_types=1);

/**
 * Company (#company) media sentence — keep inline images between words, not at row edges.
 * Alternates left/right placement on single-word rows so images do not stack on one side.
 */
function roots_normalize_media_sentence_html(string $html): string
{
    if ($html === '' || !str_contains($html, 'class="window"')) {
        return $html;
    }

    libxml_use_internal_errors(true);
    $doc = new DOMDocument('1.0', 'UTF-8');
    $loaded = $doc->loadHTML(
        '<?xml encoding="UTF-8"><div id="roots-ms-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    if (!$loaded) {
        return $html;
    }

    $root = $doc->getElementById('roots-ms-root');
    if (!$root instanceof DOMElement) {
        return $html;
    }

    $lastSide = 'right';

    foreach ($root->getElementsByTagName('p') as $paragraph) {
        if (!$paragraph instanceof DOMElement) {
            continue;
        }

        $windowNodes = [];
        foreach ($paragraph->childNodes as $child) {
            if (roots_ms_is_window_node($child)) {
                $windowNodes[] = $child;
            }
        }

        $rowClass = '';

        foreach ($windowNodes as $window) {
            $windowIndex = roots_ms_find_child_index($paragraph, $window);
            if ($windowIndex === null) {
                continue;
            }

            $nodes = roots_ms_paragraph_child_nodes($paragraph);
            $beforeNodes = array_slice($nodes, 0, $windowIndex);
            $afterNodes = array_slice($nodes, $windowIndex + 1);

            $beforeWords = roots_ms_count_words_from_nodes($beforeNodes);
            $afterWords = roots_ms_count_words_from_nodes($afterNodes);
            $totalWords = $beforeWords + $afterWords;

            $sideClass = '';

            if ($totalWords <= 1) {
                if ($lastSide === 'right') {
                    $nodes = roots_ms_place_window_before_word($beforeNodes, $window, $afterNodes);
                    $sideClass = 'roots-ms-row--img-left';
                    $lastSide = 'left';
                } else {
                    $nodes = roots_ms_place_window_after_word($beforeNodes, $window, $afterNodes);
                    $sideClass = 'roots-ms-row--img-right';
                    $lastSide = 'right';
                }
            } elseif ($beforeWords === 0 || roots_ms_nodes_are_punctuation_only($beforeNodes)) {
                [$firstWord, $rest] = roots_ms_take_first_word_nodes($afterNodes);
                $nodes = array_merge($beforeNodes, $firstWord, [$window], $rest);
                $lastSide = 'right';
            } elseif ($afterWords === 0 || roots_ms_nodes_are_punctuation_only($afterNodes)) {
                [$rest, $lastWord] = roots_ms_take_last_word_nodes($beforeNodes);
                $nodes = array_merge($rest, [$window], $lastWord, $afterNodes);
                $lastSide = 'left';
            } else {
                $nodes = array_merge($beforeNodes, [$window], $afterNodes);
                $lastSide = $afterWords >= $beforeWords ? 'right' : 'left';
            }

            roots_ms_replace_paragraph_children($paragraph, $nodes);
            if ($sideClass !== '') {
                $rowClass = trim($rowClass . ' ' . $sideClass);
            }
        }

        if ($windowNodes !== []) {
            roots_ms_wrap_paragraph_row($paragraph, trim('roots-ms-row ' . $rowClass));
        }
    }

    return roots_ms_inner_html($root);
}

function roots_ms_find_child_index(DOMElement $parent, DOMNode $target): ?int
{
    $index = 0;
    foreach ($parent->childNodes as $child) {
        if ($child === $target) {
            return $index;
        }
        $index++;
    }

    return null;
}

/** @return list<DOMNode> */
function roots_ms_paragraph_child_nodes(DOMElement $paragraph): array
{
    $nodes = [];
    foreach ($paragraph->childNodes as $child) {
        $nodes[] = $child;
    }

    return $nodes;
}

function roots_ms_is_window_node(DOMNode $node): bool
{
    return $node instanceof DOMElement
        && $node->tagName === 'span'
        && str_contains($node->getAttribute('class'), 'window');
}

function roots_ms_count_words_from_nodes(array $nodes): int
{
    $text = '';
    foreach ($nodes as $node) {
        $text .= $node->textContent ?? '';
    }

    return roots_ms_count_words($text);
}

function roots_ms_count_words(string $text): int
{
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
    if ($text === '') {
        return 0;
    }

    $count = 0;
    foreach (preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $part) {
        if (preg_match('/\p{L}/u', $part) === 1) {
            $count++;
        }
    }

    return $count;
}

function roots_ms_nodes_are_punctuation_only(array $nodes): bool
{
    $text = '';
    foreach ($nodes as $node) {
        $text .= $node->textContent ?? '';
    }

    return roots_ms_count_words($text) === 0;
}

/** @param list<DOMNode> $nodes */
function roots_ms_replace_paragraph_children(DOMElement $paragraph, array $nodes): void
{
    while ($paragraph->firstChild) {
        $paragraph->removeChild($paragraph->firstChild);
    }

    $doc = $paragraph->ownerDocument;
    if (!$doc instanceof DOMDocument) {
        return;
    }

    foreach ($nodes as $node) {
        $paragraph->appendChild($doc->importNode($node, true));
    }
}

function roots_ms_wrap_paragraph_row(DOMElement $paragraph, string $className): void
{
    $doc = $paragraph->ownerDocument;
    if (!$doc instanceof DOMDocument) {
        return;
    }

    $existing = null;
    foreach ($paragraph->childNodes as $child) {
        if ($child instanceof DOMElement
            && $child->tagName === 'span'
            && str_contains($child->getAttribute('class'), 'roots-ms-row')) {
            $existing = $child;
            break;
        }
    }

    if ($existing instanceof DOMElement) {
        roots_ms_append_element_class($existing, $className);

        return;
    }

    $wrapper = $doc->createElement('span');
    $wrapper->setAttribute('class', trim($className));

    while ($paragraph->firstChild) {
        $wrapper->appendChild($paragraph->firstChild);
    }

    $paragraph->appendChild($wrapper);
}

function roots_ms_append_element_class(DOMElement $element, string $className): void
{
    $existing = trim($element->getAttribute('class'));
    foreach (preg_split('/\s+/', $className, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $class) {
        if ($existing === '') {
            $existing = $class;
            continue;
        }
        if (!str_contains(' ' . $existing . ' ', ' ' . $class . ' ')) {
            $existing .= ' ' . $class;
        }
    }
    $element->setAttribute('class', $existing);
}

function roots_ms_append_paragraph_class(DOMElement $paragraph, string $className): void
{
    roots_ms_append_element_class($paragraph, $className);
}

function roots_ms_inner_html(DOMElement $element): string
{
    $html = '';
    foreach ($element->childNodes as $child) {
        $html .= $element->ownerDocument?->saveHTML($child) ?? '';
    }

    return $html;
}

/**
 * @param list<DOMNode> $nodes
 * @return array{0: list<DOMNode>, 1: list<DOMNode>}
 */
function roots_ms_take_first_word_nodes(array $nodes): array
{
    $firstWord = [];
    $rest = [];
    $found = false;

    foreach ($nodes as $node) {
        if ($found) {
            $rest[] = $node;
            continue;
        }

        if ($node instanceof DOMText) {
            $split = roots_ms_split_text_first_word($node);
            if ($split === null) {
                $firstWord[] = $node;
                continue;
            }

            if ($split['leading'] !== null) {
                $firstWord[] = $split['leading'];
            }
            if ($split['word'] !== null) {
                $firstWord[] = $split['word'];
                $found = true;
            }
            if ($split['rest'] !== null) {
                $rest[] = $split['rest'];
                $found = true;
            }
            continue;
        }

        $firstWord[] = $node;
        if (roots_ms_count_words($node->textContent ?? '') > 0) {
            $found = true;
        }
    }

    return [$firstWord, $rest];
}

/**
 * @param list<DOMNode> $nodes
 * @return array{0: list<DOMNode>, 1: list<DOMNode>}
 */
function roots_ms_take_last_word_nodes(array $nodes): array
{
    $reversed = array_reverse($nodes);
    [$lastWordRev, $restRev] = roots_ms_take_first_word_nodes($reversed);

    return [array_reverse($restRev), array_reverse($lastWordRev)];
}

/** @return array{leading: ?DOMText, word: ?DOMText, rest: ?DOMText}|null */
function roots_ms_split_text_first_word(DOMText $node): ?array
{
    $text = $node->textContent ?? '';
    if (!preg_match('/^(\s*)(\S+)([\s\S]*)$/u', $text, $matches)) {
        return null;
    }

    $doc = $node->ownerDocument;
    if (!$doc instanceof DOMDocument) {
        return null;
    }

    return [
        'leading' => $matches[1] !== '' ? $doc->createTextNode($matches[1]) : null,
        'word' => $doc->createTextNode($matches[2]),
        'rest' => $matches[3] !== '' ? $doc->createTextNode($matches[3]) : null,
    ];
}

/** @param list<DOMNode> $beforeNodes @param list<DOMNode> $afterNodes @return list<DOMNode> */
function roots_ms_place_window_before_word(array $beforeNodes, DOMNode $window, array $afterNodes): array
{
    if (roots_ms_count_words_from_nodes($beforeNodes) > 0) {
        return array_merge($beforeNodes, [$window], $afterNodes);
    }

    [$firstWord, $rest] = roots_ms_take_first_word_nodes($afterNodes);

    return array_merge($beforeNodes, [$window], $firstWord, $rest);
}

/** @param list<DOMNode> $beforeNodes @param list<DOMNode> $afterNodes @return list<DOMNode> */
function roots_ms_place_window_after_word(array $beforeNodes, DOMNode $window, array $afterNodes): array
{
    if (roots_ms_count_words_from_nodes($afterNodes) > 0) {
        return array_merge($beforeNodes, [$window], $afterNodes);
    }

    [$rest, $lastWord] = roots_ms_take_last_word_nodes($beforeNodes);

    return array_merge($rest, $lastWord, [$window], $afterNodes);
}
