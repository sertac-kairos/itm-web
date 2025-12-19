/**
 * Google Maps Configuration
 * Replace YOUR_API_KEY with your actual Google Maps API key
 */

export const GOOGLE_MAPS_CONFIG = {
    apiKey: window.googleMapsApiKey || 'YOUR_API_KEY', // Will be set from Laravel config
    libraries: ['places'],
    language: 'tr', // Turkish language
    region: 'TR' // Turkey region
};

export const MAP_DEFAULTS = {
    center: { lat: 38.4192, lng: 27.1287 }, // İzmir center
    zoom: 12,
    mapTypeId: 'roadmap',
    mapTypeControl: true,
    zoomControl: true,
    streetViewControl: true,
    fullscreenControl: true,
    mapTypeControlOptions: {
        style: 'HORIZONTAL_BAR',
        position: 'TOP_CENTER'
    }
};
