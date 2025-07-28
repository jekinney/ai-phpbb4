<?php

namespace App\Http\Controllers;

use App\Models\FileAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadController extends Controller
{
    /**
     * Upload file for editor.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $user = Auth::user();

        // Generate unique filename
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::random(32) . '.' . $extension;
        
        // Store file
        $filePath = $file->storeAs('attachments', $fileName, 'public');
        
        // Get file info
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $fileHash = hash_file('sha256', $file->getPathname());
        $isImage = str_starts_with($mimeType, 'image/');
        
        $metadata = [];
        
        // If it's an image, get dimensions and create thumbnail
        if ($isImage) {
            try {
                $imagePath = storage_path('app/public/' . $filePath);
                $imageInfo = getimagesize($imagePath);
                
                if ($imageInfo) {
                    $metadata = [
                        'width' => $imageInfo[0],
                        'height' => $imageInfo[1],
                    ];
                }
                
                // Create thumbnail if image is large
                if (isset($metadata['width']) && $metadata['width'] > 300) {
                    $thumbnailPath = str_replace('.', '_thumb.', $filePath);
                    $this->createThumbnail($imagePath, storage_path('app/public/' . $thumbnailPath));
                }
            } catch (\Exception $e) {
                // If image processing fails, continue without thumbnail
            }
        }

        // Create file attachment record
        $attachment = FileAttachment::create([
            'user_id' => $user->id,
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_hash' => $fileHash,
            'is_image' => $isImage,
            'metadata' => $metadata,
        ]);

        return response()->json([
            'success' => true,
            'file' => [
                'id' => $attachment->id,
                'url' => $attachment->url,
                'name' => $attachment->original_name,
                'size' => $attachment->file_size_human,
                'is_image' => $attachment->is_image,
                'thumbnail_url' => $attachment->thumbnail_url,
            ]
        ]);
    }

    /**
     * Download file.
     */
    public function download(FileAttachment $attachment)
    {
        $attachment->incrementDownloadCount();
        
        return Storage::download(
            'public/' . $attachment->file_path,
            $attachment->original_name
        );
    }

    /**
     * Delete file.
     */
    public function destroy(FileAttachment $attachment)
    {
        $this->authorize('delete', $attachment);
        
        // Delete physical file
        Storage::delete('public/' . $attachment->file_path);
        
        // Delete thumbnail if exists
        $thumbnailPath = str_replace('.', '_thumb.', $attachment->file_path);
        if (Storage::exists('public/' . $thumbnailPath)) {
            Storage::delete('public/' . $thumbnailPath);
        }
        
        // Delete database record
        $attachment->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Create thumbnail for image.
     */
    private function createThumbnail(string $sourcePath, string $thumbnailPath): void
    {
        try {
            // Simple thumbnail creation using GD (if available)
            if (extension_loaded('gd')) {
                $info = getimagesize($sourcePath);
                $width = $info[0];
                $height = $info[1];
                $mime = $info['mime'];
                
                // Calculate thumbnail dimensions
                $thumbWidth = 300;
                $thumbHeight = ($height / $width) * $thumbWidth;
                
                // Create source image
                switch ($mime) {
                    case 'image/jpeg':
                        $source = imagecreatefromjpeg($sourcePath);
                        break;
                    case 'image/png':
                        $source = imagecreatefrompng($sourcePath);
                        break;
                    case 'image/gif':
                        $source = imagecreatefromgif($sourcePath);
                        break;
                    default:
                        return;
                }
                
                // Create thumbnail
                $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                
                // Save thumbnail
                switch ($mime) {
                    case 'image/jpeg':
                        imagejpeg($thumb, $thumbnailPath, 85);
                        break;
                    case 'image/png':
                        imagepng($thumb, $thumbnailPath);
                        break;
                    case 'image/gif':
                        imagegif($thumb, $thumbnailPath);
                        break;
                }
                
                // Clean up
                imagedestroy($source);
                imagedestroy($thumb);
            }
        } catch (\Exception $e) {
            // Thumbnail creation failed, continue without
        }
    }
}
