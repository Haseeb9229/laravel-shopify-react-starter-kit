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
    <ui-nav-menu>
        <a href="/" rel="home">Dashboard</a>
        <a href="/campaigns">Campaigns</a>
        <a href="/analytics-and-reports">Analytics & Reports</a>
        <a href="/pricingplans">Pricing Plans</a>
        <a href="/support">Support</a>
    </ui-nav-menu>

    <script>

        const {

                fetch: originalFetch
            } = window;

            window.fetch = async (...args) => {
                let [resource, config] = args;

                // request interceptor here
                let token = await shopify.idToken();

                config = {
                    ...config,
                    headers: {
                        ...config?.headers,
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                }
                const response = await originalFetch(resource, config);
                // response interceptor here
                return response;
            };
    </script>
@endsection
