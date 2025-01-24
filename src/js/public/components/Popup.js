import { settings } from '../config';

export default class Popup {
	constructor( app ) {
		this.app = app;
		this.bindEvent();
	}

	bindEvent() {
		document.addEventListener( 'click', ( e ) => {
			this.unBlock( e );
		} );
	}

	showPopup( e ) {
		jQuery.blockUI( {
			message: settings.template,
			css: settings.popup.css,
			overlayCSS: settings.popup.overlay,
			fadeIn: settings.popup.fadeIn,
			fadeOut: settings.popup.fadeOut,
			focusInput: settings.popup.focusInput,
			bindEvents: false,
			timeout: 0,
			allowBodyStretch: true,
			centerX: true,
			centerY: true,
			blockMsgClass: 'blockMsg blockMsgAwooc',
			onBlock: () => {
				this.app.events.trigger( 'awooc_popup_open_trigger' );
				this.app.request.sendRequest( e );
			},
			onUnblock: () => this.app.events.trigger( 'awooc_popup_close_trigger' ),
			onOverlayClick: () => document.documentElement.style.overflow = 'initial',
		} );
	}

	unBlock( event ) {
		if ( event.target.classList.contains( 'awooc-close' ) || event.target.classList.contains( 'blockOverlay' ) ) {
			jQuery.unblockUI();
		}
	}
}
