<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Our Restaurant') }}</title>

        <!-- Favicon with reliable SVG -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgMzIgMzIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImxvZ29HcmFkIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj4KICAgICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3R5bGU9InN0b3AtY29sb3I6IzEwYjk4MTtzdG9wLW9wYWNpdHk6MSIgLz4KICAgICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdHlsZT0ic3RvcC1jb2xvcjojMDU5NjY5O3N0b3Atb3BhY2l0eToxIiAvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+CiAgPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTUiIGZpbGw9InVybCgjbG9nb0dyYWQpIiBzdHJva2U9IiNmZmZmZmYiIHN0cm9rZS13aWR0aD0iMSIvPgogIDxnIGZpbGw9IndoaXRlIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjAuNSI+CiAgICA8bGluZSB4MT0iOCIgeTE9IjgiIHgyPSI4IiB5Mj0iMjAiIHN0cm9rZS13aWR0aD0iMSIvPgogICAgPGxpbmUgeDE9IjciIHkxPSI4IiB4Mj0iOSIgeTI9IjgiIHN0cm9rZS13aWR0aD0iMSIvPgogICAgPGxpbmUgeDE9IjciIHkxPSIxMCIgeDI9IjkiIHkyPSIxMCIgc3Ryb2tlLXdpZHRoPSIxIi8+CiAgICA8bGluZSB4MT0iNyIgeTE9IjEyIiB4Mj0iOSIgeTI9IjEyIiBzdHJva2Utd2lkdGg9IjEiLz4KICAgIDxsaW5lIHgxPSIyNCIgeTE9IjgiIHgyPSIyNCIgeTI9IjIwIiBzdHJva2Utd2lkdGg9IjEiLz4KICAgIDxwb2x5Z29uIHBvaW50cz0iMjQsOCAyNiwxMCAyNCwxMiIgZmlsbD0id2hpdGUiLz4KICAgIDxjaXJjbGUgY3g9IjE2IiBjeT0iMjIiIHI9IjQiIGZpbGw9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIvPgogICAgPGNpcmNsZSBjeD0iMTYiIGN5PSIyMiIgcj0iMiIgZmlsbD0id2hpdGUiLz4KICA8L2c+Cjwvc3ZnPg==">
        <link rel="icon" type="image/x-icon" href="/favicon.ico?v={{ time() }}">
        <link rel="apple-touch-icon" sizes="180x180" href="data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgMzIgMzIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgPGRlZnM+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImxvZ29HcmFkIiB4MT0iMCUiIHkxPSIwJSIgeDI9IjEwMCUiIHkyPSIxMDAlIj4KICAgICAgPHN0b3Agb2Zmc2V0PSIwJSIgc3R5bGU9InN0b3AtY29sb3I6IzEwYjk4MTtzdG9wLW9wYWNpdHk6MSIgLz4KICAgICAgPHN0b3Agb2Zmc2V0PSIxMDAlIiBzdHlsZT0ic3RvcC1jb2xvcjojMDU5NjY5O3N0b3Atb3BhY2l0eToxIiAvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+CiAgPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTUiIGZpbGw9InVybCgjbG9nb0dyYWQpIiBzdHJva2U9IiNmZmZmZmYiIHN0cm9rZS13aWR0aD0iMSIvPgogIDxnIGZpbGw9IndoaXRlIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjAuNSI+CiAgICA8bGluZSB4MT0iOCIgeTE9IjgiIHgyPSI4IiB5Mj0iMjAiIHN0cm9rZS13aWR0aD0iMSIvPgogICAgPGxpbmUgeDE9IjciIHkxPSI4IiB4Mj0iOSIgeTI9IjgiIHN0cm9rZS13aWR0aD0iMSIvPgogICAgPGxpbmUgeDE9IjciIHkxPSIxMCIgeDI9IjkiIHkyPSIxMCIgc3Ryb2tlLXdpZHRoPSIxIi8+CiAgICA8bGluZSB4MT0iNyIgeTE9IjEyIiB4Mj0iOSIgeTI9IjEyIiBzdHJva2Utd2lkdGg9IjEiLz4KICAgIDxsaW5lIHgxPSIyNCIgeTE9IjgiIHgyPSIyNCIgeTI9IjIwIiBzdHJva2Utd2lkdGg9IjEiLz4KICAgIDxwb2x5Z29uIHBvaW50cz0iMjQsOCAyNiwxMCAyNCwxMiIgZmlsbD0id2hpdGUiLz4KICAgIDxjaXJjbGUgY3g9IjE2IiBjeT0iMjIiIHI9IjQiIGZpbGw9Im5vbmUiIHN0cm9rZS13aWR0aD0iMSIvPgogICAgPGNpcmNsZSBjeD0iMTYiIGN5PSIyMiIgcj0iMiIgZmlsbD0id2hpdGUiLz4KICA8L2c+Cjwvc3ZnPg==">
        <link rel="manifest" href="/site.webmanifest?v={{ time() }}">
        <meta name="theme-color" content="#10b981">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Font Awesome for star icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
