<?php
function load_words() {
    $path = __DIR__ . "/langdata/common_english_words.txt";
    if (!file_exists($path)) return false;
    return file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

function load_grammar() {
    $base = __DIR__ . "/langdata/";
    $nouns_path      = $base . "nouns.txt";
    $verbs_path      = $base . "verbs.txt";
    $adjectives_path = $base . "adjectives.txt";
    $adverbs_path    = $base . "adverbs.txt";
    $patterns_path   = $base . "patterns.txt";

    foreach ([$nouns_path, $verbs_path, $adjectives_path, $adverbs_path, $patterns_path] as $f) {
        if (!file_exists($f)) return false;
    }

    return [
        'nouns'      => file($nouns_path,      FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
        'verbs'      => file($verbs_path,      FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
        'adjectives' => file($adjectives_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
        'adverbs'    => file($adverbs_path,    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
        'patterns'   => file($patterns_path,   FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES),
    ];
}

// MODE 1: CPU SPIRIT
function spirit_mode($words) {
    $length   = rand(5, 15);
    $response = [];
    $count    = count($words) - 1;
    for ($i = 0; $i < $length; $i++) {
        $response[] = $words[rand(0, $count)];
    }
    return implode(" ", $response) . ".";
}

// MODE 2: CPU SPIRIT V2
function grammar_mode($lang) {
    if (!$lang) return spirit_mode(load_words());

    $nouns      = $lang['nouns'];
    $verbs      = $lang['verbs'];
    $adjectives = $lang['adjectives'];
    $adverbs    = $lang['adverbs'];
    $patterns   = $lang['patterns'];

    $pattern = $patterns[array_rand($patterns)];

    while (str_contains($pattern, '[noun]')) {
        $pattern = preg_replace('/\[noun\]/', $nouns[rand(0, count($nouns) - 1)], $pattern, 1);
    }
    while (str_contains($pattern, '[verb]')) {
        $pattern = preg_replace('/\[verb\]/', $verbs[rand(0, count($verbs) - 1)], $pattern, 1);
    }
    while (str_contains($pattern, '[adjective]')) {
        $pattern = preg_replace('/\[adjective\]/', $adjectives[rand(0, count($adjectives) - 1)], $pattern, 1);
    }
    while (str_contains($pattern, '[adverb]')) {
        $pattern = preg_replace('/\[adverb\]/', $adverbs[rand(0, count($adverbs) - 1)], $pattern, 1);
    }

    return $pattern;
}

// MAIN BRAIN
function DUMB_respond($words, $lang) {
    if (rand(0, 1) === 0 || !$lang) {
        return [
            "mode"     => "spirit",
            "label"    => "CPU SPIRIT",
            "response" => spirit_mode($words)
        ];
    } else {
        return [
            "mode"     => "spirit",
            "label"    => "CPU SPIRIT V2",
            "response" => grammar_mode($lang)
        ];
    }
}
