<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    private function superadmin(): User
    {
        return User::where('role', 'superadmin')->firstOrFail();
    }

    public function inbox()
    {
        $messages = Message::with(['sender'])
            ->where('receiver_id', auth()->id())
            ->whereNull('receiver_deleted_at')
            ->latest('sent_at')
            ->paginate(10);

        return view('instructor.messages.inbox', compact('messages'));
    }

    public function sent()
    {
        $messages = Message::with(['receiver'])
            ->where('sender_id', auth()->id())
            ->whereNull('sender_deleted_at')
            ->latest('sent_at')
            ->paginate(10);

        return view('instructor.messages.sent', compact('messages'));
    }

    public function create()
    {
        $superadmin = $this->superadmin();
        return view('instructor.messages.compose', compact('superadmin'));
    }

    public function store(Request $request)
    {
        $superadmin = $this->superadmin();

        $data = $request->validate([
            'subject' => ['nullable', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'], // 10MB
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $superadmin->id,
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'],
            'sent_at' => now(),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file) continue;

                $path = $file->store('documents/messages', ['disk' => 'local']);

                $doc = Document::create([
                    'owner_id' => auth()->id(),         // belongs to instructor
                    'uploaded_by' => auth()->id(),      // instructor uploaded
                    'title' => $data['subject'] ?? null,
                    'category' => 'message_attachment',
                    'file_type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'pdf',
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'storage_disk' => 'local',
                    'file_size' => $file->getSize(),
                    'is_active' => true,
                ]);

                $message->attachments()->attach($doc->id);
            }
        }

        return redirect()->route('instructor.messages.sent')->with('status', 'Message sent successfully.');
    }

    public function show(Message $message)
    {
        if ($message->sender_id !== auth()->id() && $message->receiver_id !== auth()->id()) {
            abort(403);
        }

        $message->load(['sender', 'receiver', 'attachments']);

        if ($message->receiver_id === auth()->id() && !$message->read_at) {
            $message->update(['read_at' => now()]);
        }

        return view('instructor.messages.show', compact('message'));
    }

public function chat()
{
    $superadmin = $this->superadmin();

    $superadminId = $superadmin->id;
    $instructorId = auth()->id();

    $messages = Message::with(['sender', 'attachments'])
        ->where(function ($q) use ($superadminId, $instructorId) {
            $q->where('sender_id', $superadminId)->where('receiver_id', $instructorId);
        })
        ->orWhere(function ($q) use ($superadminId, $instructorId) {
            $q->where('sender_id', $instructorId)->where('receiver_id', $superadminId);
        })
        ->orderBy('sent_at')
        ->get();

    // mark admin->instructor as read
    Message::where('sender_id', $superadminId)
        ->where('receiver_id', $instructorId)
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    return view('instructor.messages.chat', compact('superadmin', 'messages'));
}

public function send(Request $request)
{
    $superadmin = $this->superadmin();

    $data = $request->validate([
        'body' => ['required', 'string'],
        'attachments.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
    ]);

    $message = Message::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $superadmin->id,
        'subject' => null,
        'body' => $data['body'],
        'sent_at' => now(),
    ]);

    if ($request->hasFile('attachments')) {
        foreach ($request->file('attachments') as $file) {
            if (!$file) continue;

            $path = $file->store('documents/messages', ['disk' => 'local']);

            $doc = Document::create([
                'owner_id' => auth()->id(),
                'uploaded_by' => auth()->id(),
                'title' => null,
                'category' => 'message_attachment',
                'file_type' => str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'pdf',
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'storage_disk' => 'local',
                'file_size' => $file->getSize(),
                'is_active' => true,
            ]);

            $message->attachments()->attach($doc->id);
        }
    }

    return redirect()->route('instructor.messages.chat');
}

}
