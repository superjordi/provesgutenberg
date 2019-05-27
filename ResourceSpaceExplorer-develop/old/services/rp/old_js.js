/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

var mexp_rp_location_js_loaded = false;
var mexp_rp_location_map       = null;
var mexp_rp_location_marker    = null;
var mexp_rp_location_timeout   = null;
var pf = wp.media.view.MediaFrame.Post;

wp.media.view.MediaFrame.Post = pf.extend({

	initialize: function() {

		pf.prototype.initialize.apply( this, arguments );

		this.on( 'content:render:mexp-service-rp-content-location', _.bind( function() {

			this.state().frame.content.get().on( 'loaded', function( response ) {

				if ( ! response || ! response.meta || ! response.meta.coords )
					return;

				var ll = new google.maps.LatLng( response.meta.coords.lat, response.meta.coords.lng );

				mexp_rp_location_marker.setPosition( ll );
				mexp_rp_location_map.panTo( ll );

			} );

			if ( !mexp_rp_location_js_loaded ) {

				$('#mexp_rp_map_canvas').css( 'background-image', 'url(' + mexp.admin_url + '/images/wpspin_light.gif)');

				var script = document.createElement("script");
				script.type = "text/javascript";
				script.src = mexp.services.rp.labels.gmaps_url + '?sensor=false&callback=mexp_rp_location_initialize';
				document.body.appendChild(script);

			} else {

				mexp_rp_location_initialize();

			}

		}, this ) );

	}

});

function mexp_rp_location_initialize() {

	var callback = function() {
		mexp_rp_location_fetch( mexp_rp_location_load );
	};

	if ( navigator.geolocation ) {
		navigator.geolocation.getCurrentPosition( mexp_rp_location_load, callback );
		mexp_rp_location_timeout = window.setTimeout( callback, 8000 );
	} else {
		mexp_rp_location_fetch( callback );
	}

	mexp_rp_location_js_loaded = true;

}

function mexp_rp_location_fetch( callback ) {

	callback( {
		coords : google.loader.ClientLocation
	} );

}

function mexp_rp_location_load( position ) {

	var lat, lng;
	$ = jQuery;

	window.clearTimeout( mexp_rp_location_timeout );

	// Enable the visual refresh
	google.maps.visualRefresh = true;

	var loc = $('#mexp-rp-search-location-coords').val();

	if ( loc ) {
		ll = loc.split( ',' );
		lat = ll[0];
		lng = ll[1];
	} else {
		lat = position.coords.latitude;
		lng = position.coords.longitude;
		$('#mexp-rp-search-location-coords').val( lat + ',' + lng );
	}

	var radius = $('#mexp-rp-search-location-radius').val();
	var mapOptions = {
		center            : new google.maps.LatLng( lat, lng ),
		zoom              : 10,
		mapTypeId         : google.maps.MapTypeId.ROADMAP,
		mapTypeControl    : false,
		streetViewControl : false
	};
	mexp_rp_location_map = new google.maps.Map( document.getElementById( 'mexp_rp_map_canvas' ), mapOptions );
	mexp_rp_location_marker = new google.maps.Marker({
		position  : new google.maps.LatLng( lat, lng ),
		draggable : true,
		map       : mexp_rp_location_map
	});
	var circle = new google.maps.Circle({
		map          : mexp_rp_location_map,
		radius       : ( radius * 1000 ), // metres
		strokeWeight : 1,
		fillColor    : 'blue',
		fillOpacity  : 0.15,
		strokeColor  : '#fff'
	});
	circle.bindTo( 'center', mexp_rp_location_marker, 'position' );

	$('#mexp-rp-search-location-radius').on('change',function(){
		circle.setRadius( $(this).val() * 1000 );
	});
	$('#mexp-rp-search-location-name').on('change',function(){
		$('#mexp-rp-search-location-coords').val('');
	});

	google.maps.event.addListener(mexp_rp_location_marker, 'dragend', function() {
		p = mexp_rp_location_marker.getPosition();
		mexp_rp_location_map.panTo( p );
		$('#mexp-rp-search-location-coords').val( p.lat() + ',' + p.lng() ).closest('form').submit();
	});

}
