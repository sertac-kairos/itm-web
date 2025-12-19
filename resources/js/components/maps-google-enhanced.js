/**
* Theme: Adminto - Responsive Bootstrap 5 Admin Dashboard
* Author: Coderthemes
* Component: Enhanced Google Maps for İzmir Time Machine
*/

import GMaps from 'gmaps'
import { GOOGLE_MAPS_CONFIG, MAP_DEFAULTS } from '../config/maps.js'

'use strict';

(function () {
    // Global map instance
    let siteMap = null;
    let selectedLat = null;
    let selectedLng = null;

    // Initialize site map with Google Maps
    function initSiteMap() {
        if (siteMap) {
            // Clean up existing map
            siteMap = null;
        }

        try {
            const mapContainer = document.getElementById('siteMap');
            if (!mapContainer) {
                console.error('Map container not found');
                return;
            }

            // Clear loading content
            mapContainer.innerHTML = '';

            // Create Google Map with configuration
            siteMap = new GMaps({
                div: mapContainer,
                lat: MAP_DEFAULTS.center.lat,
                lng: MAP_DEFAULTS.center.lng,
                zoom: MAP_DEFAULTS.zoom,
                mapTypeControl: MAP_DEFAULTS.mapTypeControl,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle[MAP_DEFAULTS.mapTypeControlOptions.style],
                    position: google.maps.ControlPosition[MAP_DEFAULTS.mapTypeControlOptions.position],
                },
                zoomControl: MAP_DEFAULTS.zoomControl,
                streetViewControl: MAP_DEFAULTS.streetViewControl,
                fullscreenControl: MAP_DEFAULTS.fullscreenControl,
                mapTypeId: google.maps.MapTypeId[MAP_DEFAULTS.mapTypeId.toUpperCase()]
            });

            // Map click event
            google.maps.event.addListener(siteMap.map, 'click', function(event) {
                selectedLat = event.latLng.lat();
                selectedLng = event.latLng.lng();
                
                // Update modal fields if they exist
                const latField = document.getElementById('modalLatitude');
                const lngField = document.getElementById('modalLongitude');
                
                if (latField) {
                    latField.value = selectedLat.toFixed(6);
                }
                if (lngField) {
                    lngField.value = selectedLng.toFixed(6);
                }

                // Add marker at clicked location
                if (siteMap.markers && siteMap.markers.length > 0) {
                    siteMap.removeMarkers();
                }
                
                siteMap.addMarker({
                    lat: selectedLat,
                    lng: selectedLng,
                    title: 'Selected Location',
                    infoWindow: {
                        content: '<p>Latitude: ' + selectedLat.toFixed(6) + '<br>Longitude: ' + selectedLng.toFixed(6) + '</p>'
                    }
                });
            });

            // Add existing markers if coordinates are provided
            const existingLat = document.getElementById('modalLatitude')?.value;
            const existingLng = document.getElementById('modalLongitude')?.value;
            
            if (existingLat && existingLng) {
                siteMap.addMarker({
                    lat: parseFloat(existingLat),
                    lng: parseFloat(existingLng),
                    title: 'Current Location',
                    infoWindow: {
                        content: '<p>Current Location<br>Latitude: ' + existingLat + '<br>Longitude: ' + existingLng + '</p>'
                    }
                });
                
                // Center map on existing location
                siteMap.setCenter(parseFloat(existingLat), parseFloat(existingLng));
            }

        } catch (error) {
            console.error('Error initializing Google Map:', error);
        }
    }

    // Initialize map when modal is shown
    function initMapOnModalShow() {
        // Initialize map after modal is shown
        setTimeout(function() {
            initSiteMap();
        }, 300);
    }

    // Expose functions globally
    window.initSiteMap = initSiteMap;
    window.initMapOnModalShow = initMapOnModalShow;
    
    // Also expose as global functions for backward compatibility
    if (typeof window.openMapModal === 'undefined') {
        window.openMapModal = function() {
            // This will be overridden by individual pages
            console.warn('openMapModal not defined in current page');
        };
    }
    
    // Ensure other common functions are available
    if (typeof window.setQuickCoordinates === 'undefined') {
        window.setQuickCoordinates = function(lat, lng, name) {
            console.warn('setQuickCoordinates not defined in current page');
        };
    }
    
    if (typeof window.selectCoordinates === 'undefined') {
        window.selectCoordinates = function() {
            console.warn('selectCoordinates not defined in current page');
        };
    }
    
    if (typeof window.resetMap === 'undefined') {
        window.resetMap = function() {
            console.warn('resetMap not defined in current page');
        };
    }

    // Auto-initialize if map container exists on page load
    document.addEventListener('DOMContentLoaded', function() {
        const mapContainer = document.getElementById('siteMap');
        if (mapContainer) {
            // Load Google Maps API if not already loaded
            if (typeof google === 'undefined' || !google.maps) {
                loadGoogleMapsAPI().then(() => {
                    initSiteMap();
                });
            } else {
                initSiteMap();
            }
        }
    });

    // Function to load Google Maps API
    function loadGoogleMapsAPI() {
        return new Promise((resolve, reject) => {
            if (typeof google !== 'undefined' && google.maps) {
                resolve();
                return;
            }

            const script = document.createElement('script');
            const libraries = GOOGLE_MAPS_CONFIG.libraries.join(',');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_CONFIG.apiKey}&libraries=${libraries}&language=${GOOGLE_MAPS_CONFIG.language}&region=${GOOGLE_MAPS_CONFIG.region}`;
            script.async = true;
            script.defer = true;
            
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Failed to load Google Maps API'));
            
            document.head.appendChild(script);
        });
    }

})();
