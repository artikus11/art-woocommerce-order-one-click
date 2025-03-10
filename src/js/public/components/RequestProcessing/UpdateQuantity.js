import { settings, translate } from '../../config';

export default class UpdateQuantity {
	constructor( toMail, request, event ) {
		this.toMail = toMail;
		this.request = request;
		this.qtyVal = '';

		this.cache = request.cache;
		this.event = event;
	}

	bindEvent() {
		const popupQtyContainer = document.querySelector( '.awooc-popup-qty' );
		if ( ! popupQtyContainer ) {
			return;
		}

		const quantityInput = popupQtyContainer.querySelector( '.awooc-popup-input-qty' );
		if ( quantityInput ) {
			this.initializeQuantityInput( quantityInput );
		}
	}

	initializeQuantityInput( quantityInput ) {
		this.setMaxValueInput( quantityInput );
		this.updateAll();

		quantityInput.addEventListener( 'input', ( e ) => this.handleInputEvent( e ) );

		this.handlerPlusMinusButtonsEvent( quantityInput );
	}

	handlerPlusMinusButtonsEvent( quantityInput ) {
		const minusButton = document.querySelector( '.awooc-popup-input-qty--minus' );
		const plusButton = document.querySelector( '.awooc-popup-input-qty--plus' );
		if ( minusButton && plusButton ) {
			minusButton.addEventListener( 'click', () => this.updateInputQuantity( quantityInput, 'decrease' ) );
			plusButton.addEventListener( 'click', () => this.updateInputQuantity( quantityInput, 'increase' ) );
		}
	}

	updateInputQuantity( inputElement, action ) {
		const currentValue = parseFloat( inputElement.value );
		const step = this.getSafeValue( inputElement.step, 1 );
		const minValue = this.getSafeValue( inputElement.min, -Infinity );
		const maxValue = this.getSafeValue( inputElement.max, Infinity );

		let newValue = currentValue + ( action === 'decrease' ? -step : step );

		const decimalPlaces = Math.max( 0, -Math.floor( Math.log10( step ) ) ); // Определяем количество знаков после запятой для step
		newValue = parseFloat( newValue.toFixed( decimalPlaces ) ); // Округляем до нужного количества знаков

		if ( newValue >= minValue && newValue <= maxValue ) {
			inputElement.value = newValue;
			inputElement.dispatchEvent( new Event( 'input', { bubbles: true } ) );
		}
	}

	handleInputEvent( e, input = null ) {
		if ( ! input ) {
			input = e.target.closest( '.awooc-popup-input-qty' );
		}

		if ( ! input ) {
			return;
		}

		this.setMaxValueInput( input );

		this.qtyVal = input.value;

		this.updateAll();
	}

	setMaxValueInput( input ) {
		const minValue = this.getSafeValue( input.min, input.step );
		const maxValue = this.getSafeValue( input.max, input.value );

		input.value = Math.min( Math.max( parseFloat( String( input.value ) ) || minValue, minValue ), maxValue );

		this.qtyVal = input.value;
	}

	updateAll() {
		this.updateMailQuantity();
		this.updateAmount();
		this.updateMailData();
		this.updateProductQuantity();
		this.updateAnalytics();
	}

	updateMailQuantity() {
		this.toMail.qty = `${ translate.product_qty }${ this.qtyVal }`;
	}

	updateMailData() {
		const hiddenDataField = document.querySelector( 'input[name="awooc-hidden-data"]' );
		if ( hiddenDataField ) {
			hiddenDataField.value = this.request.fillDataToMail( this.toMail );
		}
	}

	updateAnalytics() {
		this.request.app.analyticData.qty = this.qtyVal;
	}

	updateProductQuantity() {
		const productQtyField = document.querySelector( 'input[name="awooc_product_qty"]' );
		if ( productQtyField ) {
			productQtyField.value = this.qtyVal;
		}
	}

	updateAmount() {
		const priceValue = this.getPrice();
		if ( ! priceValue ) {
			return;
		}

		const amount = this.displayPrice( this.parsePrice( priceValue ) * this.qtyVal );

		this.updateDOMAmount( amount );
		this.updateMailAmount();
	}

	getPrice() {
		const priceElement = document.querySelector( '.awooc-popup-price .woocommerce-Price-currencyValue' );
		return priceElement?.textContent?.replace( /\s+/g, '' ) || null;
	}

	getSafeValue( value, defaultValue ) {
		return value !== '' && ! Number.isNaN( parseFloat( value ) ) ? parseFloat( value ) : defaultValue;
	}

	getPriceSettings() {
		const {
			price_num_decimals: rawDecimalPlaces = 0,
			price_decimal_sep: rawDecimalSeparator = '.',
			price_thousand_sep: rawThousandSeparator = '',
		} = settings.popup;

		const decimalSeparator = rawDecimalSeparator || '.';
		const thousandSeparator = rawThousandSeparator || '';
		const decimalPlaces = rawDecimalPlaces || 0;

		return { decimalPlaces, decimalSeparator, thousandSeparator };
	}

	displayPrice( input ) {
		const sanitizedInput = String( input ).replace( /[^0-9.]/g, '' );
		const number = parseFloat( sanitizedInput );

		if ( isNaN( number ) ) {
			return 'Invalid number';
		}

		const { decimalPlaces, decimalSeparator, thousandSeparator } = this.getPriceSettings();

		// Разделяем на целую и дробную части
		const [ integerPart, decimalPart ] = String( number.toFixed( decimalPlaces ) ).split( '.' );

		// Форматируем целую часть с разделителями тысяч
		const formattedIntegerPart = integerPart.replace( /\B(?=(\d{3})+(?!\d))/g, thousandSeparator );

		// Форматируем дробную часть, если требуется
		const formattedDecimalPart = decimalPlaces > 0
			? decimalPart.padEnd( decimalPlaces, '0' ).slice( 0, decimalPlaces )
			: '';

		return decimalPlaces > 0 && formattedDecimalPart
			? `${ formattedIntegerPart }${ decimalSeparator }${ formattedDecimalPart }`
			: formattedIntegerPart;
	}

	parsePrice( number ) {
		number = number ?? 0;

		const { decimalPlaces, decimalSeparator, thousandSeparator } = this.getPriceSettings();

		// Функция для экранирования символов в регулярных выражениях
		const escapeRegExp = ( string ) => string.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' );

		if ( typeof number !== 'number' ) {
			number = String( number );

			[ thousandSeparator, ' ', '.', ',' ]
				.filter( Boolean ) // Игнорируем пустые значения
				.forEach( ( sep ) => {
					if ( sep !== decimalSeparator ) { // Исключаем десятичный разделитель
						number = number.replace( new RegExp( escapeRegExp( sep ), 'g' ), '' );
					}
				} );

			// Нормализуем десятичный разделитель к точке (.)
			if ( decimalSeparator !== '.' ) {
				number = number.replace( new RegExp( escapeRegExp( decimalSeparator ), 'g' ), '.' );
			}

			// Удаляем лишние точки и все символы, кроме цифр, точки и минуса
			number = number.replace( /\.+(?![^.]+$)|[^0-9.-]/g, '' );
		}

		number = parseFloat( number ) || 0;

		if ( decimalPlaces !== false ) {
			const decimalsCount = String( decimalPlaces ) === '' ? 2 : parseInt( decimalPlaces, 10 ); // По умолчанию 2 знака
			return number.toFixed( decimalsCount );
		}

		return number.toFixed( 20 );
	}

	updateDOMAmount( amount ) {
		const sumElement = document.querySelector( '.awooc-popup-sum .woocommerce-Price-currencyValue' );
		if ( sumElement ) {
			sumElement.textContent = amount;
		}
	}

	updateMailAmount() {
		const currentAmountElement = document.querySelector( '.awooc-popup-sum bdi' );
		if ( currentAmountElement ) {
			this.toMail.sum = `${ translate.formatted_sum }${ currentAmountElement.textContent }`;
		} else {
			delete this.toMail.sum;
		}
	}
}
