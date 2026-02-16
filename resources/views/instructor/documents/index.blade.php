@extends('layouts.instructor')

@section('title', 'Documenten')
@section('breadcrumb', 'Instructeur / Documenten')

@section('content')
<div class="space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">Mijn documenten</h1>
            <p class="text-gray-600 text-sm mt-1">
                Upload en beheer je PDF’s en afbeeldingen.
            </p>
        </div>
    </div>

    {{-- Upload --}}
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('instructor.documents.store') }}" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @csrf

            <div class="md:col-span-1">
                <x-input-label for="file" value="Bestand (PDF/Afbeelding)" />
                <input id="file" type="file" name="file"
                       accept=".pdf,.png,.jpg,.jpeg,.webp"
                       class="block mt-1 w-full text-sm"
                       required>
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
            </div>

            <div class="md:col-span-1">
                <x-input-label for="title" value="Titel (optioneel)" />
                <x-text-input id="title" name="title" type="text" class="block mt-1 w-full"
                              value="{{ old('title') }}" />
                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="md:col-span-1">
                <x-input-label for="category" value="Categorie (optioneel)" />
                <x-text-input id="category" name="category" type="text" class="block mt-1 w-full"
                              value="{{ old('category') }}" placeholder="algemeen / certificaat / enz." />
                <x-input-error :messages="$errors->get('category')" class="mt-2" />
            </div>

            <div class="md:col-span-3 flex items-center gap-3">
                <x-primary-button class="bg-btn-logoblue">Uploaden</x-primary-button>
                <span class="text-sm text-gray-500">
                    Max 10MB • pdf, png, jpg, jpeg, webp
                </span>
            </div>
        </form>
    </div>

    {{-- Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($documents as $doc)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                {{-- Preview --}}
                <div class="h-40 bg-gray-50 flex items-center justify-center">
                   @if($doc->file_type === 'image')
                        <img src="{{ route('instructor.documents.preview', $doc->id) }}"
                            alt="voorbeeld"
                            class="w-full h-full object-cover" />
                    @else
                        <div class="text-sm text-gray-600">📄 PDF</div>
                    @endif
                </div>

                <div class="p-4">
                    <div class="font-semibold truncate">
                        {{ $doc->title ?: $doc->original_name }}
                    </div>

                    <div class="text-xs text-gray-600 mt-1">
                        <span class="inline-block px-2 py-1 bg-gray-100 rounded">
                            {{ $doc->category }}
                        </span>
                        <span class="ml-2">{{ strtoupper($doc->extension) }}</span>
                    </div>

                    <div class="text-xs text-gray-500 mt-2">
                        Geüpload op: {{ optional($doc->created_at)->format('d M Y') }}
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <a href="{{ route('instructor.documents.download', $doc->id) }}"
                           class="text-sm text-indigo-600 hover:text-indigo-800 relative z-10">
                            Downloaden
                        </a>

                        <form method="POST"
                              action="{{ route('instructor.documents.destroy', $doc->id) }}"
                              class="relative z-10"
                              onclick="event.stopPropagation();"
                              onsubmit="return confirm('Dit bestand permanent verwijderen?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    onclick="event.stopPropagation();"
                                    class="text-sm text-red-600 hover:text-red-800">
                                Verwijderen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-lg shadow p-6 text-gray-600">
                Nog geen documenten geüpload.
            </div>
        @endforelse
    </div>

    <div>
        {{ $documents->links() }}
    </div>
</div>
@endsection
