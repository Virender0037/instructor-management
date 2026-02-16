@extends('layouts.instructor')

@section('title', 'Messages')
@section('breadcrumb', 'Instructor / Messages')

@section('content')
<div class="bg-white rounded-lg shadow flex flex-col">
    <div class="p-4 border-b">
        <div class="font-semibold">Chat with Super Admin</div>
        <div class="text-sm text-gray-600">{{ $superadmin->email }}</div>
    </div>
    @php
    $me = (int) auth('instructor')->id();
    @endphp
    <div class="p-4 flex-1 overflow-auto space-y-3 max-h-[60vh]">
        @forelse($messages as $msg)
             @php
            $mine = (int) $msg->sender_id === $me;
            @endphp
            <div class="flex @if($mine) justify-end @else justify-start @endif">
                <div class="max-w-[75%] rounded-lg px-4 py-2
                    @if($mine) bg-slate-200 text-gray-900 @else bg-gray-100 text-gray-900 @endif">
                    <div class="text-sm whitespace-pre-line">{{ $msg->body }}</div>
                    @if($msg->attachments->count())
                        <div class="mt-2 space-y-2">
                            @foreach($msg->attachments as $att)
                                @if($att->file_type === 'image')
                                    <a href="{{ route('instructor.documents.download', $att->id) }}" class="block">
                                        <img
                                            src="{{ route('instructor.documents.preview', $att->id) }}"
                                            alt="attachment"
                                            class="rounded-lg w-3xs border @if($mine) border-indigo-400/40 @else border-gray-200 @endif"
                                            loading="lazy"
                                        >
                                    </a>
                                    <div class="text-[11px] @if($mine) text-neutral-600 @else text-gray-500 @endif">
                                        🖼️ {{ $att->original_name }}
                                    </div>
                                @else
                                    <a href="{{ route('instructor.documents.download', $att->id) }}"
                                       class="text-xs underline text-gray-700">
                                        📄 {{ $att->original_name }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-1 text-[11px] @if($mine) text-neutral-500 @else text-gray-500 @endif">
                        {{ optional($msg->sent_at)->format('d M, h:i A') }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-gray-600 text-sm">No messages yet.</div>
        @endforelse
    </div>

    <form method="POST"
          action="{{ route('instructor.messages.send') }}"
          enctype="multipart/form-data"
          class="p-4 border-t space-y-2">
        @csrf

        <textarea name="body" rows="2"
            class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Type a message..." required>{{ old('body') }}</textarea>

        <div class="flex items-center justify-between gap-3">
            <input type="file" name="attachments[]" multiple
                   accept=".pdf,.png,.jpg,.jpeg,.webp"
                   class="file:mr-4 file:rounded-full file:border-0 file:bg-violet-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-violet-600 dark:file:text-violet-100 dark:hover:file:bg-violet-500" />

            <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Send
            </button>
        </div>

        @if($errors->any())
            <div class="text-sm text-red-600">
                {{ $errors->first() }}
            </div>
        @endif
    </form>
</div>
@endsection
