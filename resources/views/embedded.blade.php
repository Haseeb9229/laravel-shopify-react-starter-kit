@extends('shopify-app::layouts.default')

@section('styles')
    @routes
    @viteReactRefresh
    @vite(['resources/js/app.jsx'])
    {{-- @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"]) --}}
    @inertiaHead
@endsection

@section('content')
    @inertia
@endsection

@section('scripts')
    @parent

    <script>

    </script>
@endsection
