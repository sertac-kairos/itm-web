<!-- App js -->
@vite(['resources/js/app.js'])

<!-- Google Maps API Key -->
<script>
    window.googleMapsApiKey = '{{ config("services.google_maps.api_key") }}';
</script>

@yield('scripts')
@stack('scripts')