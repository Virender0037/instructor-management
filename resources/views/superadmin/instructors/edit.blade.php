@extends('layouts.superadmin')

@section('title', 'Instructeur Bewerken')
@section('breadcrumb', 'Superbeheerder / Instructeurs / Bewerken')

@section('content')
<div class="max-w-2xl space-y-4">

    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-semibold">Instructeur Bewerken</h1>
        <p class="text-gray-600 text-sm mt-1">Werk naam/email bij. Bij wijziging van het e-mailadres is opnieuw verificatie vereist.</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('superadmin.instructors.update', $user->id) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="name" value="Naam" />
                <x-text-input id="name" name="name" class="block mt-1 w-full"
                    value="{{ old('name', $user->name) }}" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="E-mailadres" />
                <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                    value="{{ old('email', $user->email) }}" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="pt-2 flex gap-2">
                <x-primary-button class="bg-btn-logoblue">Opslaan</x-primary-button>
                <a href="{{ route('superadmin.instructors.index') }}"
                   class="px-4 py-2 bg-gray-100 rounded-md hover:bg-gray-200">
                    Terug
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
