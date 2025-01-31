import AppCore from './AppCore';
import { setupAwoocEventHandling } from './helpers';

( function( $ ) {
	'use strict';

	setupAwoocEventHandling( $ );

	$( document ).ready( () => {
		if ( ! window.AwoocAppCore ) {
			window.AwoocAppCore = new AppCore( $ );
		}
	} );
}( jQuery ) );
