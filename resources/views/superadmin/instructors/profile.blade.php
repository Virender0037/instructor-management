@extends('layouts.superadmin')

@section('title', 'Instructeursprofiel')
@section('breadcrumb', 'Superbeheerder / Instructeurs / Profiel')

@section('content')

<div class="max-w-3xl space-y-4">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-xl font-semibold">Instructeursprofiel</h1>
                <p class="text-gray-600 text-sm mt-1">{{ $user->name }} • {{ $user->email }}</p>
            </div>

            <a href="{{ route('superadmin.instructors.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900">
                Terug naar lijst
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('superadmin.instructors.profile.update', $user->id) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="telefoonnummer" value="Telefoonnummer" />
                    <x-text-input id="telefoonnummer" name="telefoonnummer" class="block mt-1 w-full"
                        value="{{ old('telefoonnummer', $user->instructorProfile->telefoonnummer) }}" />
                    <x-input-error :messages="$errors->get('telefoonnummer')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="dob" value="Geboortedatum" />
                    <x-text-input id="dob" name="dob" type="date" class="block mt-1 w-full"
                        value="{{ old('dob', $user->instructorProfile->dob) }}" />
                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="wagennummer" value="Wagennummer" />
                    <x-text-input id="wagennummer" name="wagennummer" type="text"
                                class="block mt-1 w-full"
                                value="{{ old('wagennummer', $user->instructorProfile->wagennummer ?? '') }}" />
                    <x-input-error :messages="$errors->get('wagennummer')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="auto" value="Auto" />
                    <select id="auto" name="auto" class="block mt-1 w-full">
                        <option value="">-- Select --</option>
                        @foreach(['Volkswagen','Mercedes','Audi'] as $brand)
                            <option value="{{ $brand }}" @selected(old('auto', $user->instructorProfile->auto ?? '') === $brand)>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('auto')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="experience_years" value="Ervaring (Jaren)" />
                    <x-text-input id="experience_years" name="experience_years" type="number" min="0" max="60"
                        class="block mt-1 w-full"
                        value="{{ old('experience_years', $user->instructorProfile->experience_years) }}" />
                    <x-input-error :messages="$errors->get('experience_years')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="specialization" value="Specialisatie" />
                    <x-text-input id="specialization" name="specialization" class="block mt-1 w-full"
                        value="{{ old('specialization', $user->instructorProfile->specialization) }}" />
                    <x-input-error :messages="$errors->get('specialization')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="address" value="Adres" />
                <textarea id="address" name="address" rows="3"
                    class="block mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $user->instructorProfile->address) }}</textarea>
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="bio" value="Bio" />
                <textarea id="bio" name="bio" rows="4"
                    class="block mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('bio', $user->instructorProfile->bio) }}</textarea>
                <x-input-error :messages="$errors->get('bio')" class="mt-2" />
            </div>

            <div class="pt-2">
                <x-primary-button class="bg-btn-logoblue">Opslaan</x-primary-button>
            </div>
        </form>
    </div>
</div>
@endsection
