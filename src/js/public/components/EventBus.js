export default class EventBus {
	constructor() {
		this.events = {};
	}

	on( event, callback ) {
		if ( ! this.events[ event ] ) {
			this.events[ event ] = [];
		}

		this.events[ event ].push( callback );

		const listener = ( e ) => {
			this.events[ event ].forEach( ( cb ) => cb( e, e.detail ) );
		};

		this.events[ event ].listener = listener;

		document.addEventListener( event, listener );
	}

	off( event, callback ) {
		if ( this.events[ event ] ) {
			this.events[ event ] = this.events[ event ].filter( ( cb ) => cb !== callback );

			if ( ! this.events[ event ]?.length ) {
				document.removeEventListener( event, this.events[ event ].listener );

				delete this.events[ event ].listener;
			}
		}
	}

	trigger( event, data = {} ) {
		const customEvent = new CustomEvent( event, { detail: data } );

		document.dispatchEvent( customEvent );
	}
}
