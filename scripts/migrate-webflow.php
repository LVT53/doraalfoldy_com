<?php

require __DIR__.'/../vendor/autoload.php';

use Illuminate\Support\Str;

$sourceDir = __DIR__.'/../da-export';
$targetDir = __DIR__.'/../resources/views/pages';
$componentDir = __DIR__.'/../resources/views/components/webflow';

if (! is_dir($targetDir)) {
    mkdir($targetDir, 0775, true);
}
if (! is_dir($componentDir)) {
    mkdir($componentDir, 0775, true);
}

$files = glob("$sourceDir/*.html");

foreach ($files as $file) {
    $filename = basename($file);
    $pageName = Str::replaceLast('.html', '', $filename);
    if ($pageName === 'index') {
        $pageName = 'home';
    }

    echo "Processing $filename -> $pageName.blade.php\n";

    $html = file_get_contents($file);

    // Use DOMDocument for reliable extraction
    $doc = new DOMDocument;
    @$doc->loadHTML('<?xml encoding="UTF-8">'.$html);
    $xpath = new DOMXPath($doc);

    // Remove Navbar, Footer, and Scripts
    $navbars = $xpath->query("//div[contains(@class, 'navbar')]");
    foreach ($navbars as $nav) {
        $nav->parentNode->removeChild($nav);
    }

    $footers = $xpath->query("//div[contains(@class, 'footer')]");
    foreach ($footers as $footer) {
        $footer->parentNode->removeChild($footer);
    }

    $scripts = $xpath->query('//script');
    foreach ($scripts as $script) {
        $script->parentNode->removeChild($script);
    }

    $body = $doc->getElementsByTagName('body')->item(0);
    $content = '';
    foreach ($body->childNodes as $node) {
        $content .= $doc->saveHTML($node);
    }

    $cleanContent = $content;

    // Asset Sanitization

    // images/XYZ -> /images/content/XYZ
    $cleanContent = preg_replace('/src="images\/(.*?)"/i', 'src="/images/content/$1"', $cleanContent);
    $cleanContent = preg_replace('/srcset="images\/(.*?)"/i', 'srcset="/images/content/$1"', $cleanContent);

    // Link Sanitization
    // href="page.html" -> href="{{ route('page') }}"
    $cleanContent = preg_replace_callback('/href="([a-z0-9\-]+)\.html"/i', function ($m) {
        $route = $m[1] === 'index' ? 'home' : $m[1];

        return 'href="{{ route(\''.$route.'\') }}"';
    }, $cleanContent);

    // Replace <img> with <x-ui.responsive-image />
    $cleanContent = preg_replace_callback('/<img([^>]+)>/i', function ($m) {
        $attrs = $m[1];
        preg_match('/src="([^"]+)"/', $attrs, $srcMatch);
        preg_match('/alt="([^"]*)"/', $attrs, $altMatch);
        preg_match('/class="([^"]*)"/', $attrs, $classMatch);

        $src = $srcMatch[1] ?? '';
        $alt = $altMatch[1] ?? '';
        $class = $classMatch[1] ?? '';

        return "<x-ui.responsive-image src=\"$src\" alt=\"$alt\" class=\"$class\" />";
    }, $cleanContent);

    // De-Webflow Classes
    $classMap = [
        'w-button' => 'inline-block px-6 py-2 rounded-full transition-all duration-300',
        'w-inline-block' => 'inline-block',
        'w-nav-brand' => '',
        'w-nav-menu' => '',
        'w-nav' => '',
        'w-nav-link' => '',
        'w--current' => 'active',
        'shadow-three' => 'shadow-md',
        'body' => 'bg-white',
    ];

    foreach ($classMap as $old => $new) {
        $cleanContent = str_replace($old, $new, $cleanContent);
    }

    // Convert Webflow's Section widths to site-container if they are top-level
    $cleanContent = str_replace('class="section"', 'class="site-container"', $cleanContent);
    $cleanContent = str_replace('class="header-section"', 'class="site-container header-section"', $cleanContent);

    // Wrap in Layout

    $blade = "@extends('layouts.page')\n\n";
    $blade .= "@section('title', '".($pageName === 'home' ? 'Kezdőlap' : ucfirst($pageName))." | Alföldy Dóra')\n\n";
    $blade .= "@section('page')\n";
    $blade .= $cleanContent;
    $blade .= "\n@endsection\n";

    file_put_contents("$targetDir/$pageName.blade.php", $blade);
}

echo "Migration complete!\n";
