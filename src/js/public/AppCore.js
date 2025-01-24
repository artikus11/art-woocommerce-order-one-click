import EventBus from './components/EventBus';
import Popup from './components/Popup';
import Buttons from './components/Buttons';
import Request from './components/Request';
import Form from './components/Form';
import VariationSwatchesByCartFlows from './components/Integrations/VariationSwatchesByCartFlows';
import Woodmart from './components/Integrations/Woodmart';

export default class AppCore {
	constructor( $ ) {
		if ( ! this.validateGlobals() ) {
			return;
		}

		this.$ = $;
		this.xhr = false;
		this.analyticData = {};

		this.events = new EventBus();

		this.popup = new Popup( this );
		this.buttons = new Buttons( this );
		this.request = new Request( this );
		this.form = new Form( this );

		this.variationSwatches = new VariationSwatchesByCartFlows( this );
		this.woodmart = new Woodmart( this );

		this.init();
	}

	init() {
		this.buttons.init();
		this.form.init();
		this.variationSwatches.init();
		this.woodmart.init();
	}

	validateGlobals() {
		const globals = {
			awooc_scripts_ajax: 'awooc_scripts_ajax not found',
			awooc_scripts_translate: 'awooc_scripts_translate not found',
			awooc_scripts_settings: 'awooc_scripts_settings not found',
			wpcf7: 'На странице не существует объекта wpcf7. Что-то не так с темой...',
		};

		for ( const key in globals ) {
			if ( typeof window[ key ] === 'undefined' || window[ key ] === null ) {
				// eslint-disable-next-line no-console
				console.warn( globals[ key ] );
				return false;
			}
		}

		return true;
	}
}
