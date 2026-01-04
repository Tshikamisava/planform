<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'change_request_id',
        'document_type',
        'title',
        'description',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'mime_type',
        'file_hash',
        'version',
        'parent_document_id',
        'is_latest',
        'is_public',
        'access_level',
        'uploaded_by',
        'download_count',
        'last_downloaded_at',
    ];

    protected $casts = [
        'version' => 'integer',
        'is_latest' => 'boolean',
        'is_public' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'last_downloaded_at' => 'datetime',
    ];

    public function changeRequest()
    {
        return $this->belongsTo(Dcr::class, 'change_request_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function parentDocument()
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function childDocuments()
    {
        return $this->hasMany(Document::class, 'parent_document_id');
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
        $this->update(['last_downloaded_at' => now()]);
    }
}
