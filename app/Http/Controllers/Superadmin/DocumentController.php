<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function download(Document $document)
    {
        $disk = $document->storage_disk ?? 'local';

        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk($disk)->download($document->file_path, $document->original_name);
    }

   public function index(Request $request)
    {
        $query = Document::with(['owner', 'uploader'])->latest();

        if ($request->filled('owner_id')) {
            $query->where('owner_id', (int) $request->input('owner_id'));
        }

        if ($request->filled('type')) {
            $query->where('file_type', $request->input('type'));
        }

        $documents = $query->paginate(10)->withQueryString();

        $instructors = User::where('role', 'instructor')->orderBy('name')->get();

        return view('superadmin.documents.index', compact('documents', 'instructors'));
    }

    public function store(Request $request)
{
    $data = $request->validate([
        'owner_id' => ['required', 'integer', 'exists:users,id'],
        'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        'title' => ['nullable', 'string', 'max:200'],
        'category' => ['nullable', 'string', 'max:50'],
    ]);

    $owner = User::findOrFail($data['owner_id']);
    if ($owner->role !== 'instructor') {
        abort(403);
    }

    $file = $request->file('file');
    $path = $file->store('documents/uploads', ['disk' => 'local']);

    $doc = Document::create([
        'owner_id' => $owner->id,
        'uploaded_by' => auth()->id(),
        'title' => $data['title'] ?? null,
        'category' => $data['category'] ?? 'general',
        'file_type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'pdf',
        'mime_type' => $file->getMimeType(),
        'extension' => $file->getClientOriginalExtension(),
        'original_name' => $file->getClientOriginalName(),
        'file_path' => $path,
        'storage_disk' => 'local',
        'file_size' => $file->getSize(),
        'is_active' => true,
    ]);

    audit('document.uploaded', $doc, [
        'owner_id' => $doc->owner_id,
        'original_name' => $doc->original_name,
        'uploaded_by' => $doc->uploaded_by,
    ]);

    return back()->with('status', 'Document uploaded for instructor successfully.');
}


    public function preview(Document $document)
    {
        if ($document->file_type !== 'image') {
            abort(404);
        }

        $disk = $document->storage_disk ?? 'local';

        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk($disk)->response($document->file_path);
    }

    public function destroy(Document $document)
    {
        if ($document->category === 'message_attachment') {
            return back()->with('status', 'This file is a chat attachment. Delete from message feature later.');
        }

        $disk = $document->storage_disk ?? 'local';

        if ($document->file_path && Storage::disk($disk)->exists($document->file_path)) {
            Storage::disk($disk)->delete($document->file_path);
        }

        $document->delete();

        audit('document.deleted', $document, [
            'owner_id' => $document->owner_id,
            'original_name' => $document->original_name,
        ]);

        return back()->with('status', 'Document deleted successfully.');
    }

}
