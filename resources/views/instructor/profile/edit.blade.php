@extends('layouts.instructor')

@section('title', 'Profiel')
@section('breadcrumb', 'Instructeur / Profiel')

@section('content')
<div class="max-w-3xl space-y-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-xl font-semibold">Mijn profiel</h1>
        <p class="text-gray-600 text-sm mt-1">{{ $user->name }} • {{ $user->email }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('instructor.profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="phone" value="Telefoonnummer" />
                    <x-text-input id="phone" name="phone" class="block mt-1 w-full"
                        value="{{ old('phone', $user->instructorProfile->telefoonnummer) }}" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="dob" value="Geboortedatum" />
                    <x-text-input id="dob" name="dob" type="date" class="block mt-1 w-full"
                        value="{{ old('dob', $user->instructorProfile->dob) }}" />
                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="experience_years" value="Ervaring (jaren)" />
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
                <x-input-label for="bio" value="Over mij" />
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
