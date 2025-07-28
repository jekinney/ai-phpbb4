# Admin Guide

## Admin Dashboard

The admin dashboard provides a central location for managing your forum. Access it at `/admin` after logging in with admin privileges.

### Main Features

#### Documentation Viewer
- Browse and read documentation files
- Search through documentation content
- View markdown files with proper formatting
- Download documentation files

#### File Manager
- View file upload statistics
- Manage user attachments
- Monitor storage usage
- Delete unwanted files
- Filter files by type and user

#### Static Pages Manager
- Create custom pages
- Edit existing static content
- Manage page permissions
- Preview pages before publishing

### Permissions

Admin permissions are configured in the ACL system:

- `admin.access`: Basic admin dashboard access
- `admin.view_documentation`: View documentation files
- `admin.manage_attachments`: Manage file attachments
- `admin.upload_files`: Upload files through admin interface
- `admin.delete_any_attachment`: Delete any user's attachments
- `admin.view_file_stats`: View file statistics
- `admin.manage_file_settings`: Configure file upload settings

### File Management

#### Supported File Types
- Images: JPG, PNG, GIF, WebP
- Documents: PDF, DOC, DOCX, TXT
- Archives: ZIP, RAR
- Media: MP4, MP3

#### Upload Limits
- Maximum file size: 10MB (configurable)
- Maximum files per post: 5 (configurable)
- Thumbnail generation for images
- Virus scanning (if configured)

### Security

- All admin routes require authentication
- Permission checks on every action
- File type validation
- Size limits enforced
- XSS protection on file names
