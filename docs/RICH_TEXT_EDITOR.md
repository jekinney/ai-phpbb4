# Rich Text Editor Implementation

## Overview
The custom rich text editor integrates TinyMCE with Livewire to provide a powerful content creation experience with support for embedded files and images.

## Features

### ✅ Rich Text Editing
- **WYSIWYG Interface**: Full visual editing experience
- **Formatting Tools**: Bold, italic, underline, alignment, lists
- **Semantic HTML**: Clean, semantic markup output
- **Dark Mode Support**: Automatic theme switching

### ✅ File Upload Support
- **Image Upload**: Drag & drop or file picker for images
- **Media Files**: Support for video and audio files
- **File Management**: Automatic thumbnail generation for images
- **File Types**: Images, videos, audio, PDFs, documents

### ✅ Livewire Integration
- **Real-time Sync**: Content syncs with Livewire models
- **Component Lifecycle**: Proper cleanup and reinitialization
- **Form Validation**: Integrates with Livewire validation

## Components

### 1. RichTextEditor Component
**File**: `app/Livewire/RichTextEditor.php`

**Properties**:
- `$content` - The editor content
- `$editorId` - Unique identifier for the editor instance
- `$placeholder` - Placeholder text
- `$height` - Editor height in pixels
- `$enableFileUpload` - Enable/disable file uploads
- `$maxFileSize` - Maximum file size in KB
- `$allowedTypes` - Allowed file types

**Usage**:
```blade
<livewire:rich-text-editor 
    :content="$content"
    editor-id="my-editor"
    placeholder="Start writing..."
    :height="400"
/>
```

### 2. FileAttachment Model
**File**: `app/Models/FileAttachment.php`

**Features**:
- Polymorphic relationships (attach to any model)
- File metadata storage (size, type, dimensions)
- Automatic thumbnail generation
- Download tracking
- Security features (file hash, validation)

### 3. File Upload Controller
**File**: `app/Http/Controllers/FileUploadController.php`

**Endpoints**:
- `POST /files/upload` - Upload new file
- `GET /files/{attachment}/download` - Download file
- `DELETE /files/{attachment}` - Delete file

## Technical Implementation

### File Upload Flow
1. **User selects file** → TinyMCE file picker or drag & drop
2. **JavaScript uploads file** → AJAX request to `/files/upload`
3. **Server processes file** → Validation, storage, thumbnail generation
4. **Database record created** → FileAttachment model with metadata
5. **URL returned** → TinyMCE inserts image/link in editor

### File Storage Structure
```
storage/app/public/attachments/
├── [random_filename].jpg
├── [random_filename]_thumb.jpg
├── [random_filename].pdf
└── ...
```

### Database Schema
```sql
file_attachments:
├── id
├── attachable_type (morphable)
├── attachable_id (morphable)
├── user_id
├── original_name
├── file_name
├── file_path
├── mime_type
├── file_size
├── file_hash
├── is_image
├── metadata (JSON)
├── download_count
└── timestamps
```

## Security Features

### File Validation
- **Size Limits**: Configurable maximum file size (default 10MB)
- **Type Validation**: MIME type and extension checking
- **Malicious File Detection**: Hash-based duplicate detection
- **Virus Scanning**: Ready for integration with antivirus services

### Access Control
- **Upload Permissions**: Only authenticated users can upload
- **Ownership**: Users can only delete their own files
- **Admin Override**: Administrators can manage all files
- **Policy-based**: Uses Laravel policies for authorization

### Storage Security
- **Random Filenames**: Prevents directory traversal
- **Separate Storage**: Files stored outside web root
- **Access Logging**: Download tracking for audit trails

## Configuration

### File Upload Limits
Edit `config/filesystems.php` or `.env`:
```env
UPLOAD_MAX_SIZE=10240  # 10MB in KB
ALLOWED_FILE_TYPES=image/*,video/*,audio/*,.pdf,.doc,.docx
```

### TinyMCE Configuration
Customize in `resources/js/app.js`:
```javascript
const defaultOptions = {
    height: 300,
    plugins: ['image', 'media', 'link', 'lists', 'code'],
    toolbar: 'bold italic | link image | code',
    // ... more options
};
```

## Usage Examples

### Basic Editor
```blade
<livewire:rich-text-editor 
    wire:model="content"
    editor-id="post-content"
    placeholder="Write your post..."
/>
```

### Advanced Editor with Custom Height
```blade
<livewire:rich-text-editor 
    :content="$post->content"
    editor-id="edit-post-{{ $post->id }}"
    placeholder="Edit your post..."
    :height="500"
    wire:key="editor-{{ $post->id }}"
/>
```

### In Form Components
```php
// In your Livewire component
public $content = '';

public function submit()
{
    $this->validate(['content' => 'required|min:10']);
    
    $post = Post::create([
        'content' => $this->content,
        'user_id' => auth()->id(),
    ]);
    
    // Attachments are automatically linked via TinyMCE URLs
}
```

## Troubleshooting

### Common Issues

1. **Editor not loading**
   - Check JavaScript console for errors
   - Ensure TinyMCE assets are built with Vite
   - Verify CSRF token is present

2. **File uploads failing**
   - Check storage permissions
   - Verify storage link exists: `php artisan storage:link`
   - Check file size limits in PHP and server config

3. **Livewire sync issues**
   - Ensure unique editor IDs
   - Check wire:key attributes for dynamic components
   - Verify component lifecycle hooks

### Debug Mode
Enable debug mode in `resources/js/app.js`:
```javascript
// Add to TinyMCE config
console.log('TinyMCE initializing:', selector);
```

## Browser Support
- **Modern Browsers**: Chrome, Firefox, Safari, Edge (latest 2 versions)
- **Mobile**: iOS Safari, Chrome Mobile
- **Fallback**: Plain textarea for unsupported browsers

## Performance
- **Lazy Loading**: TinyMCE loads on demand
- **File Optimization**: Automatic image compression and thumbnails
- **CDN Ready**: Prepared for CDN integration
- **Caching**: Built-in file caching and optimization
