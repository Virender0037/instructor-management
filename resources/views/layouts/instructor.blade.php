<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Instructor') - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
      integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <style>
        .bg-custom-blue{
            background-color: #eff0fd;
        }
        .bg-btn-logoblue{
           background-color: #41499b !important;
        }
        .text-logoblue{
            color: #41499b;
        }
        button{
            background-color: revert-layer;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
<div class="min-h-screen flex">

<!-- Sidebar -->
<aside class="w-64 bg-white border-r min-h-screen flex flex-col">
    <!-- Header -->
    <div class="h-16 flex items-center px-6 border-b">
        <div class="text-xl font-semibold text-gray-800 tracking-tight">
            <img src="{{ asset('img/rijschoolhers-logo.png') }}" alt="Rijschoolhers logo">
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-6 text-sm text-gray-700 bg-custom-blue">

        <!-- Dashboard -->
        <a href="{{ route('instructor.dashboard') }}"
           class="flex items-center px-3 py-2 rounded-lg transition
           {{ request()->routeIs('instructor.dashboard')
                ? 'bg-gray-100 text-gray-900 font-semibold'
                : 'hover:bg-gray-50' }}">
            Dashboard
        </a>

        <!-- Main -->
        <div>
            <!-- Messages -->
            <a href="{{ route('instructor.messages.chat') }}"
               class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-50 transition">
                <span>Berichten</span>

                @if(!empty($unreadCount))
                    <span class="ml-2 text-xs font-medium bg-red-600 text-white px-2 py-0.5 rounded-full">
                        {{ $unreadCount }}
                    </span>
                @endif
            </a>

            <!-- Documents -->
            <a href="{{ route('instructor.documents.index') }}"
               class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition">
                Documenten / Bestanden
            </a>

            <!-- Profile -->
            <a href="{{ route('instructor.profile.edit') }}"
               class="flex items-center px-3 py-2 rounded-lg hover:bg-gray-50 transition">
                Mijn profiel
            </a>
        </div>

        <!-- Account -->
        <div>
            <div class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wide">
                Account
            </div>

            <form method="POST" action="{{ route('instructor.logout') }}" class="mt-4 mx-3">
                @csrf
                <button type="submit"
                        class="text-center flex bg-btn-logoblue text-white px-4 py-2 rounded-lg">
                    Uitloggen
                </button>
            </form>
        </div>
    </nav>
</aside>


    <!-- Main content -->
    <div class="flex-1">
        <header class="h-16 bg-blue border-b flex items-center justify-between px-6 bg-theblue">
            <div class="text-sm text-gray-600">
                @yield('breadcrumb', 'Dashboard')
            </div>

            <div class="text-sm flex items-center justify-between">
                <div class="mr-8">
                    <span class="v-btn__content" data-no-activator="">
                        <div class="v-badge v-badge--dot">
                            <div class="v-badge__wrapper">
                                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img" width="22" height="22" viewBox="0 0 24 24">
                                    <g fill="none" stroke="#374fa0" stroke-width="1.5">
                                        <path d="M2 12c0-4.714 0-7.071 1.464-8.536C4.93 2 7.286 2 12 2s7.071 0 8.535 1.464C22 4.93 22 7.286 22 12s0 7.071-1.465 8.535C19.072 22 16.714 22 12 22s-7.071 0-8.536-1.465C2 19.072 2 16.714 2 12Z"/>
                                        <path stroke-linecap="round" opacity=".5"
                                            d="M2 13h3.16c.905 0 1.358 0 1.756.183s.692.527 1.281 1.214l.606.706c.589.687.883 1.031 1.281 1.214s.85.183 1.756.183h.32c.905 0 1.358 0 1.756-.183s.692-.527 1.281-1.214l.606-.706c.589-.687.883-1.031 1.281-1.214S17.934 13 18.84 13H22M8 7h8m-6 3.5h4"/>
                                    </g>
                                </svg>
                                @if(!empty($unreadCount))
                                    <span class="ml-2 text-xs font-medium bg-red-600 text-white px-2 py-0.5 rounded-full">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </span>
                </div>
                <div class="user_info">
                    <img src="{{ asset('img/user.png') }}" alt="user-icon" style="display: inline; width: 25px; margin-right: 10px;">
                    <span class="text-gray-600">Hi,</span>
                    <span class="font-semibold">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>
        <main class="p-6">
            @if (session('status'))
                <div class="mb-4 rounded bg-green-50 text-green-700 px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>

</div>
</body>
</html>
