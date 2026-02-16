<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function inbox()
    {
        $messages = Message::with(['sender'])
            ->where('receiver_id', auth()->id())
            ->whereNull('receiver_deleted_at')
            ->latest('sent_at')
            ->paginate(10);

        return view('superadmin.messages.inbox', compact('messages'));
    }

    public function sent()
    {
        $messages = Message::with(['receiver'])
            ->where('sender_id', auth()->id())
            ->whereNull('sender_deleted_at')
            ->latest('sent_at')
            ->paginate(10);

        return view('superadmin.messages.sent', compact('messages'));
    }

    public function threads()
    {
        $instructors = User::where('role', 'instructor')->orderBy('name')->get();
        return view('superadmin.messages.threads', compact('instructors'));
    }

    public function chat(User $instructor)
    {
        if ($instructor->role !== 'instructor') abort(404);

        $superadminId = auth()->id();
        $instructorId = $instructor->id;

        $messages = Message::with(['sender', 'attachments'])
            ->where(function ($q) use ($superadminId, $instructorId) {
                $q->where('sender_id', $superadminId)->where('receiver_id', $instructorId);
            })
            ->orWhere(function ($q) use ($superadminId, $instructorId) {
                $q->where('sender_id', $instructorId)->where('receiver_id', $superadminId);
            })
            ->orderBy('sent_at')
            ->get();

        Message::where('sender_id', $instructorId)
            ->where('receiver_id', $superadminId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $instructors = User::where('role', 'instructor')->orderBy('name')->get();

        return view('superadmin.messages.chat', compact('instructor', 'instructors', 'messages'));
    }


    public function create(Request $request)
    {
        $instructors = User::where('role', 'instructor')->where('status', 1)->orderBy('name')->get();

        $to = $request->integer('to');

        return view('superadmin.messages.compose', compact('instructors', 'to'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => ['required', 'integer', 'exists:users,id'],
            'subject' => ['nullable', 'string', 'max:200'],
            'body' => ['required', 'string'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'], // 10MB each
        ]);

        $receiver = User::findOrFail($data['receiver_id']);

        if ($receiver->role !== 'instructor') {
            abort(403);
        }

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $receiver->id,
            'subject' => $data['subject'] ?? null,
            'body' => $data['body'],
            'sent_at' => now(),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file) continue;

                $path = $file->store('documents/messages', ['disk' => 'local']);

                $doc = Document::create([
                    'owner_id' => $receiver->id,        // belongs to instructor
                    'uploaded_by' => auth()->id(),      // superadmin uploaded
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

        return redirect()->route('superadmin.messages.sent')->with('status', 'Message sent successfully.');
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

        return view('superadmin.messages.show', compact('message'));
    }


    public function send(Request $request, User $instructor)
    {
        if ($instructor->role !== 'instructor') abort(404);

        $data = $request->validate([
            'body' => ['required', 'string'],
            'attachments.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $instructor->id,
            'subject' => null,
            'body' => $data['body'],
            'sent_at' => now(),
        ]);

        audit('message.sent', $message, [
            'to' => $message->receiver_id,
            'attachments' => $message->attachments()->count(),
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if (!$file) continue;

                $path = $file->store('documents/messages', ['disk' => 'local']);

                $doc = Document::create([
                    'owner_id' => $instructor->id,
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

        return redirect()->route('superadmin.messages.chat', $instructor->id);
    }

}
