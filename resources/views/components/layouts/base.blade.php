<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('external-styles-scripts')
</head>
<body class="relative min-h-screen font-sans antialiased bg-base-100">

    {{ $slot }}

    {{--  TOAST area --}}
    <x-toast position="toast-top toast-center" />
    <x-theme-toggle class="hidden" />

    @livewireScripts
</body>
@stack('scripts')
</html>