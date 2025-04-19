<?php
// Blog Plugin for HentaiCMS
// Located in plugins/blog.php

// Ensure we're running within HentaiCMS
if (!defined('ROOT_DIR')) {
    http_response_code(403);
    exit('Access Denied');
}

define('BLOG_PAGES_DIR', PLUGINS_DIR . '/blogpages');

// Check if blogpages directory exists
if (!is_dir(BLOG_PAGES_DIR) || !is_readable(BLOG_PAGES_DIR)) {
    displayHentaiError('Blog pages directory not found or not readable');
    exit();
}

// Handle blog page requests
$blogPage = isset($_GET['blog']) && is_string($_GET['blog']) 
    ? preg_replace('/[^a-zA-Z0-9\-\/]/', '', trim($_GET['blog'], '/ ')) 
    : 'index';

// Handle specific blog post requests
if ($blogPage !== 'index') {
    $markdownPaths = [
        BLOG_PAGES_DIR . '/' . $blogPage . '/index.md',
        BLOG_PAGES_DIR . '/' . $blogPage . '.md',
    ];

    foreach ($markdownPaths as $path) {
        if (file_exists($path)) {
            displayHentaiMarkdown($path, $Parsedown);
            exit();
        }
    }
}

// Show blog index for ?blog or ?blog=index
displayBlogIndex();

function displayBlogIndex() {
    global $Parsedown, $themeFile;
    
    displayHentaiHeader();
    echo '<div class="markdown-content">';
    echo '<h1>Blog Posts</h1>';
    
    $posts = [];
    $files = scandir(BLOG_PAGES_DIR);
    
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
            $postName = pathinfo($file, PATHINFO_FILENAME);
            $postPath = BLOG_PAGES_DIR . '/' . $file;
            $content = file_get_contents($postPath);
            
            // Extract title from first h1 if present
            preg_match('/^# (.+)$/m', $content, $matches);
            $title = $matches[1] ?? $postName;
            
            $posts[] = [
                'name' => $postName,
                'title' => $title,
                'file' => $postPath
            ];
        }
    }
    
    // Sort posts by filename (you could modify this to sort by date if you add metadata)
    usort($posts, function($a, $b) {
        return strcmp($b['name'], $a['name']);
    });
    
    if (empty($posts)) {
        echo '<p>No blog posts found.</p>';
    } else {
        echo '<ul>';
        foreach ($posts as $post) {
            echo '<li><a href="?blog=' . htmlspecialchars($post['name'], ENT_QUOTES, 'UTF-8') . '">' . 
                 htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') . '</a></li>';
        }
        echo '</ul>';
    }
    
    echo '</div>';
    displayHentaiFooter();
}
?>