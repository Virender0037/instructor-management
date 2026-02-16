@extends('layouts.superadmin')

@section('title', 'Documenten')
@section('breadcrumb', 'Superbeheerder / Documenten')

@section('content')

<div class="space-y-4">

    <div class="flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold">Documenten / Bestanden</h1>
        <p class="text-gray-600 text-sm mt-1">
            Uploaden voor instructeurs en alle documenten beheren.
        </p>
    </div>
</div>


    {{-- Upload for Instructor --}}
   <div class="bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('superadmin.documents.store') }}" enctype="multipart/form-data"
          class="grid grid-cols-1 md:grid-cols-5 gap-4">
        @csrf

        <div class="md:col-span-1">
            <x-input-label for="owner_id" value="Instructeur" />
            <select id="owner_id" name="owner_id"
                    class="block mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    required>
                <option value="">Selecteer instructeur</option>
                @foreach($instructors as $inst)
                    <option value="{{ $inst->id }}" {{ old('owner_id') == $inst->id ? 'selected' : '' }}>
                        {{ $inst->name }} ({{ $inst->email }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('owner_id')" class="mt-2" />
        </div>

        <div class="md:col-span-1">
            <x-input-label for="file" value="Bestand (PDF/Afbeelding)" />
            <input id="file" type="file" name="file" accept=".pdf,.png,.jpg,.jpeg,.webp"
                   class="file:mr-4 file:rounded-full file:border-0 file:bg-violet-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-violet-600 dark:file:text-violet-100 dark:hover:file:bg-violet-500"
                   aria-describedby="file_input_help" required>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">
                Max. 10 MB • pdf, png, jpg, jpeg, webp.
            </p>

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
                          value="{{ old('category') }}" placeholder="algemeen / overeenkomst / enz." />
            <x-input-error :messages="$errors->get('category')" class="mt-2" />
        </div>

        <div class="md:col-span-1 flex items-center gap-3">
            <x-primary-button class="bg-btn-logoblue">Uploaden</x-primary-button>
        </div>
    </form>
</div>


    {{-- Filters --}}
<div class="bg-white rounded-lg shadow p-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <select name="owner_id"
                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Alle instructeurs</option>
                @foreach($instructors as $inst)
                    <option value="{{ $inst->id }}" {{ request('owner_id') == $inst->id ? 'selected' : '' }}>
                        {{ $inst->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <select name="type"
                    class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Alle types</option>
                <option value="pdf" {{ request('type') === 'pdf' ? 'selected' : '' }}>PDF</option>
                <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Afbeelding</option>
            </select>
        </div>

        <div class="md:col-span-2 flex gap-2">
            <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" type="submit">
                Toepassen
            </button>
            <a href="{{ route('superadmin.documents.index') }}"
               class="px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200">
                Resetten
            </a>
        </div>
    </form>
</div>


    {{-- Grid --}}
   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($documents as $doc)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="h-40 bg-gray-50 flex items-center justify-center">
                @if($doc->file_type === 'image')
                    <img src="{{ route('superadmin.documents.preview', $doc->id) }}"
                        alt="preview"
                        class="w-full h-full object-cover" />
                @else
                    <div class="text-sm text-gray-600">📄 PDF</div>
                @endif
            </div>

            <div class="p-4">
                <div class="font-semibold truncate">
                    {{ $doc->title ?: $doc->original_name }}
                </div>

                <div class="text-xs text-gray-600 mt-1 space-y-1">
                    <div>
                        <span class="inline-block px-2 py-1 bg-gray-100 rounded">
                            {{ $doc->category }}
                        </span>
                        <span class="ml-2">{{ strtoupper($doc->extension) }}</span>
                    </div>

                    <div class="text-gray-500">
                        Eigenaar: <span class="text-gray-800">{{ optional($doc->owner)->name ?? '—' }}</span>
                    </div>

                    <div class="text-gray-500">
                        Geüpload door: <span class="text-gray-800">{{ optional($doc->uploader)->name ?? '—' }}</span>
                    </div>

                    <div class="text-gray-500">
                        {{ optional($doc->created_at)->format('d M Y') }}
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <a href="{{ route('superadmin.documents.download', $doc->id) }}"
                       class="text-sm text-indigo-600 hover:text-indigo-800">
                        Download
                    </a>

                    <form method="POST" action="{{ route('superadmin.documents.destroy', $doc->id) }}"
                          onsubmit="return confirm('Dit bestand permanent verwijderen?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="text-sm text-red-600 hover:text-red-800">
                            Verwijderen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-lg shadow p-6 text-gray-600">
            Geen documenten gevonden.
        </div>
    @endforelse
</div>


    <div>
        {{ $documents->links() }}
    </div>

</div>
@endsection
