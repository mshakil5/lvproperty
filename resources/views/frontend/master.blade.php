<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale())}}">
@php
    $company = App\Models\CompanyDetails::firstOrCreate();
@endphp

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    <meta property="og:type" content="website">

    @if($company->google_site_verification)
    <meta name="google-site-verification" content="{{ $company->google_site_verification }}">
    @endif

    <link href="{{ asset('images/company/' . $company->fav_icon) }}" rel="icon">
    
</head>

<body>

    @yield('content')

    @yield('script')

</body>

</html>