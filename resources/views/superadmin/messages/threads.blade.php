@extends('layouts.superadmin')

@section('title', 'Berichten')
@section('breadcrumb', 'Superbeheerder / Berichten')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-semibold">Berichten</h1>
    <p class="text-gray-600 mt-1">Selecteer een instructeur om de chat te openen.</p>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach($instructors as $inst)
            <a href="{{ route('superadmin.messages.chat', $inst->id) }}"
               class="p-4 rounded border hover:bg-gray-50">
                <div class="font-semibold">{{ $inst->name }}</div>
                <div class="text-sm text-gray-600">{{ $inst->email }}</div>
            </a>
        @endforeach
    </div>
</div>
@endsection
