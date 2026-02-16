<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function download(Document $document)
    {   
        if ((int) $document->owner_id !== (int) auth('instructor')->id()) {
            abort(403);
        }

        $disk = $document->storage_disk ?? 'local';

        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk($disk)->download($document->file_path, $document->original_name);
    }

        public function index()
        {
            $documents = Document::where('owner_id', auth()->id())
                ->latest()
                ->paginate(10);

            return view('instructor.documents.index', compact('documents'));
        }

        public function store(Request $request)
        {
            $data = $request->validate([
                'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'], // 10MB
                'title' => ['nullable', 'string', 'max:200'],
                'category' => ['nullable', 'string', 'max:50'],
            ]);

            $file = $request->file('file');
            $path = $file->store('documents/uploads', ['disk' => 'local']);

            Document::create([
                'owner_id' => auth()->id(),
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

            return back()->with('status', 'Document uploaded successfully.');
        }

        public function destroy(Document $document)
        {
            
            // if((int) $document->uploaded_by !== (int) auth('instructor')->id()){
            //     return back()->with('status', 'This attachment was uploaded by a Super Admin. Are you sure you want to delete it?');
            //     }

            // if ($document->category === 'message_attachment') {
            //     if((int) $document->uploaded_by !== (int) auth('instructor')->id()){
            //     return back()->with('status', 'This chat attachment was uploaded by a Super Admin. Are you sure you want to delete it?');
            //     }
            // }

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

}
