<?php

require_once 'vendor/autoload.php';

use App\Models\Post;

// Simulate quote parsing functionality
function parseQuotes($content)
{
    // Pattern to match [quote=username]content[/quote]
    $pattern = '/\[quote=([^\]]+)\](.*?)\[\/quote\]/s';
    
    return preg_replace_callback($pattern, function ($matches) {
        $author = htmlspecialchars($matches[1]);
        $quotedContent = trim($matches[2]);
        
        return sprintf(
            '<div class="bg-gray-50 dark:bg-gray-800 border-l-4 border-blue-400 p-3 my-3 rounded-r">
                <div class="text-sm text-blue-600 dark:text-blue-400 font-semibold mb-2">%s wrote:</div>
                <div class="text-sm text-gray-700 dark:text-gray-300 italic">%s</div>
            </div>',
            $author,
            nl2br(htmlspecialchars($quotedContent))
        );
    }, $content);
}

// Test quote parsing
$testContent = '[quote=John Doe]This is a test quote from John Doe.
Multiple lines are supported.[/quote]

This is my reply to John\'s comment.';

echo "Original content:\n";
echo $testContent . "\n\n";

echo "Parsed content:\n";
echo parseQuotes($testContent) . "\n\n";

// Test with multiple quotes
$multiQuoteContent = '[quote=Alice]First quote from Alice[/quote]

My response to Alice.

[quote=Bob]Second quote from Bob[/quote]

My response to Bob.';

echo "Multi-quote original:\n";
echo $multiQuoteContent . "\n\n";

echo "Multi-quote parsed:\n";
echo parseQuotes($multiQuoteContent) . "\n\n";

echo "Quote functionality test completed successfully!\n";
