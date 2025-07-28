# Quote Functionality Implementation

## Overview
The quote functionality allows users to quote other posts when replying to forum topics. When a user clicks the "Quote" button on any post, the quoted content is automatically added to their reply with proper formatting.

## How It Works

### Components Updated
1. **PostEdit Component** (`app/Livewire/PostEdit.php`)
   - Added `quotePost()` method that dispatches a `quotePost` event with the post ID

2. **PostReply Component** (`app/Livewire/PostReply.php`)
   - Added quote-related properties: `$quotedPostId`, `$quotedContent`, `$quotedAuthor`
   - Added `handleQuotePost()` method to handle incoming quote events
   - Added `clearQuote()` method to remove quotes
   - Enhanced `submit()` method to include quoted content in BBCode format

3. **Post Model** (`app/Models/Post.php`)
   - Added `parseQuotes()` method to convert BBCode quotes to HTML
   - Enhanced `processContent()` method to handle quote parsing

### User Interface
1. **Quote Button** - Added to each post's action menu
2. **Quote Preview** - Shows quoted content with author name in the reply form
3. **Quote Removal** - Users can clear quotes before submitting
4. **Auto-scroll** - Automatically scrolls to reply form when quoting

### Quote Format
- **Storage**: `[quote=username]quoted content[/quote]`
- **Display**: Styled quote boxes with author attribution

## Usage
1. User clicks "Quote" button on any post
2. Reply form opens with quoted content preview
3. User can add their own response below the quote
4. User can remove the quote if needed
5. When submitted, the quote is formatted and stored as BBCode
6. When displayed, quotes are rendered as styled HTML blocks

## Features
- ✅ Visual quote preview in reply form
- ✅ BBCode-style storage format
- ✅ HTML rendering with proper styling
- ✅ Multiple quotes support
- ✅ Quote removal functionality
- ✅ Smooth scrolling to reply form
- ✅ Author attribution
- ✅ Responsive design with dark mode support

## Technical Details
- Quotes are stored as BBCode: `[quote=username]content[/quote]`
- Real-time communication between components using Livewire events
- Quote content is limited to 300 characters in preview (full content in post)
- Supports nested HTML and multiline content
- XSS protection through proper escaping
