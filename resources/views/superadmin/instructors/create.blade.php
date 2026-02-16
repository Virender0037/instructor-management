@extends('layouts.superadmin')

@section('title', 'Instructeur Aanmaken')
@section('breadcrumb', 'Superbeheerder / Instructeurs / Aanmaken')

@section('content')
    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-xl font-semibold">Instructeur Aanmaken</h1>
            <p class="text-gray-600 mt-1">Er wordt een e-mail gestuurd voor verificatie en een link om een wachtwoord in te stellen.</p>

            <form method="POST" action="{{ route('superadmin.instructors.store') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <x-input-label for="name" value="Naam" />
                    <x-text-input id="name" name="name" type="text" class="block mt-1 w-full"
                                  value="{{ old('name') }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="email" value="E-mailadres" />
                    <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                                  value="{{ old('email') }}" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Actief</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactief</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-primary-button class="bg-btn-logoblue">Aanmaken</x-primary-button>
                    <a href="{{ route('superadmin.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        Annuleren
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
