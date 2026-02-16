@extends('layouts.superadmin')

@section('title', 'Instructeurs')
@section('breadcrumb', 'Superbeheerder / Instructeurs')

@section('content')

<div class="space-y-4">
 <div class="flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold">Instructeurs / Trainers</h1>
        <p class="text-gray-600 text-sm mt-1">
            Beheer instructeurs, verificatiestatus en toegangsrechten.
        </p>
    </div>

    <a href="{{ route('superadmin.instructors.create') }}"
       class="inline-flex items-center px-4 py-2 bg-btn-logoblue text-white rounded-md hover:bg-indigo-700">
        Instructeur aanmaken
    </a>
 </div>


    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
    <form method="GET" class="flex gap-3 md:flex-row md:items-center md:justify-between">

    <!-- Left: Search -->
    <div class="w-full md:max-w-md">
        <input type="text"
               name="q"
               value="{{ request('q') }}"
               placeholder="Zoeken op naam of e-mailadres..."
               class="w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
    </div>

    <!-- Right: Filters + Actions -->
    <div class="flex flex-wrap items-center gap-2">
        <!-- Status -->
        <select name="status"
                class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Alle statussen</option>
            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Actief</option>
            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactief</option>
        </select>

        <!-- Verified -->
        <select name="verified"
                class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Alle verificaties</option>
            <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Geverifieerd</option>
            <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Niet geverifieerd</option>
        </select>

        <!-- Apply -->
        <button type="submit"
                class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-800 focus:bg-green-800 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Toepassen
        </button>

        <!-- Reset -->
        <a href="{{ route('superadmin.instructors.index') }}"
           class="px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200">
            Resetten
        </a>
    </div>
</form>

</div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="text-left px-4 py-3">Naam</th>
                <th class="text-left px-4 py-3">E-mailadres</th>
                <th class="text-left px-4 py-3">Status</th>
                <th class="text-left px-4 py-3">Geverifieerd</th>
                <th class="text-left px-4 py-3">Aangemaakt</th>
                <th class="text-right px-4 py-3">Acties</th>
            </tr>
        </thead>
        <tbody class="divide-y">
        @forelse ($instructors as $inst)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $inst->name }}</td>
                <td class="px-4 py-3">{{ $inst->email }}</td>

                <td class="px-4 py-3">
                    @if($inst->status)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-700">
                            Actief
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-200 text-gray-700">
                            Inactief
                        </span>
                    @endif
                </td>

                <td class="px-4 py-3">
                    @if($inst->email_verified_at)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-green-100 text-green-700">
                            Geverifieerd
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-700">
                            Niet geverifieerd
                        </span>
                    @endif
                </td>

                <td class="px-4 py-3 text-gray-600">
                    {{ optional($inst->created_at)->format('d M Y') }}
                </td>

                <td class="px-4 py-3">
                    <div class="flex justify-end gap-2">
                        {{-- placeholder buttons for next steps --}}
                        <a href="{{ route('superadmin.messages.chat', $inst->id) }}"
                           class="px-3 py-1 rounded bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                            Bericht
                        </a>

                        @if(!$inst->email_verified_at)
                            <form method="POST"
                                  action="{{ route('superadmin.instructors.resendVerification', $inst) }}">
                                @csrf
                                <button type="submit"
                                        class="px-2 py-1 rounded bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                    Verificatie opnieuw verzenden
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('superadmin.instructors.toggleStatus', $inst) }}">
                            @csrf
                            @method('PATCH')

                            <button type="submit"
                                    class="px-3 py-1 rounded {{ $inst->status ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}">
                                {{ $inst->status ? 'Uitschakelen' : 'Inschakelen' }}
                            </button>
                        </form>

                        <a href="{{ route('superadmin.instructors.edit', $inst->id) }}"
                           class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">
                            Bewerken
                        </a>

                        <a href="{{ route('superadmin.instructors.profile.edit', $inst->id) }}"
                           class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">
                            Profiel
                        </a>

                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-600">
                    Geen instructeurs gevonden.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>


    <div>
        {{ $instructors->links() }}
    </div>
</div>
@endsection
