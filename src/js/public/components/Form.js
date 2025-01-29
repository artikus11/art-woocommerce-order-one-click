import { settings } from '../config';

/*global wpcf7 */
export default class Form {
	constructor( app ) {
		this.app = app;
		/* eslint-disable camelcase */
		const { cf7_form_id, mailsent_timeout, invalid_timeout } = settings.popup;
		this.formId = Number( cf7_form_id );
		this.mailsentTimeout = mailsent_timeout;
		this.invalidTimeout = invalid_timeout;
		/* eslint-disable camelcase */
	}

	init() {
		this.bindEvents();
	}

	bindEvents() {
		document.addEventListener( 'wpcf7mailsent', ( event ) => this.handleMailSent( event ) );
		document.addEventListener( 'wpcf7invalid', ( event ) => this.handleInvalid( event ) );

		this.app.events.on( 'awooc_popup_ajax_trigger', () => this.setupFormSubmitListener() );
	}

	setupFormSubmitListener() {
		const form = document.querySelector( 'form.wpcf7-form' );
		if ( form ) {
			form.addEventListener( 'submit', ( event ) => this.handleFormSubmit( event ) );
		}
	}

	handleMailSent( event ) {
		const { detail } = event;

		setTimeout( () => jQuery.unblockUI(), this.mailsentTimeout );

		if ( this.formId === detail.contactFormId ) {
			this.app.events.trigger( 'awooc_mail_sent_trigger', {
				selectedProduct: this.app.analyticData,
				mailDetail: detail,
			} );
		}
	}

	handleInvalid( event ) {
		const { detail } = event;

		if ( this.formId === detail.contactFormId ) {
			this.app.events.trigger( 'awooc_mail_invalid_trigger' );
		}

		setTimeout( () => this.clearFormErrors(), this.invalidTimeout );
	}

	clearFormErrors() {
		const formOutput = document.querySelector( '.awooc-form-custom-order .wpcf7-response-output' );
		const notValidTips = document.querySelectorAll( '.awooc-form-custom-order .wpcf7-not-valid-tip' );
		const submitButton = document.querySelector( '.awooc-form-custom-order input[type="submit"]' );

		if ( formOutput ) {
			formOutput.innerHTML = '';
		}

		if ( submitButton ) {
			submitButton.disabled = false;
		}
		notValidTips.forEach( ( tip ) => tip.remove() );
	}

	handleFormSubmit( event ) {
		const submitButton = event.currentTarget.querySelector( 'input[type="submit"]' );
		if ( submitButton ) {
			submitButton.disabled = true;
		}
	}

	initContactForm() {
		document.querySelectorAll( '.awooc-form-custom-order div.wpcf7 > form' ).forEach( ( form ) => {
			const versionInput = form.querySelector( 'input[name="_wpcf7_version"]' );
			if ( ! versionInput ) {
				return;
			}

			const isOldVersion = versionInput.value && versionInput.value <= '5.4';
			if ( isOldVersion ) {
				this.initOldWpcf7( form );
			} else {
				wpcf7.init( form );
			}
		} );
	}

	initOldWpcf7( form ) {
		wpcf7.initForm( form );
		if ( wpcf7.cached ) {
			wpcf7.refill( form );
		}
	}

	initMask() {
		document.querySelectorAll( '.awooc-form-custom-order .wpcf7-mask' ).forEach( ( field ) => {
			const dataMask = jQuery( field ).data( 'mask' );
			if ( ! dataMask ) {
				return;
			}

			try {
				jQuery( field ).mask( dataMask );
				if ( ! /[a*]/.test( dataMask ) ) {
					field.setAttribute( 'inputmode', 'numeric' );
				}
			} catch ( e ) {
				// eslint-disable-next-line no-console
				console.error( `Error ${ e.name }: ${ e.message }\n${ e.stack }` );
			}
		} );
	}
}
