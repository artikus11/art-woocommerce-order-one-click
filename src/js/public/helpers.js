/**
 * Выводим предупреждение об измении обработки события только в момент срабатывания события
 * @param {jQuery} $ - Объект jQuery.
 */
export function setupAwoocEventHandling( $ ) {
	const EVENT_PREFIX = 'awooc_';
	const originalOn = $.fn.on;
	const originalTrigger = $.fn.trigger;

	// Патчим $.fn.on
	$.fn.on = function( event, selector, data, handler ) {
		// Нормализация аргументов
		if ( typeof selector === 'function' ) {
			handler = selector;
			selector = undefined;
			data = undefined;
		} else if ( typeof data === 'function' ) {
			handler = data;
			data = undefined;
		}

		// Если событие начинается с префикса "awooc_", добавляем обертку для обработчика
		if ( typeof event === 'string' && event.startsWith( EVENT_PREFIX ) && handler ) {
			const wrappedHandler = function( e ) {
				// Выводим предупреждение, если обработчик ожидает второй аргумент (data)
				if ( handler.length > 1 ) {
					// eslint-disable-next-line no-console
					console.warn(
						`[WARNING] '${ event }' передает данные через event.detail, но обработчик ожидает data. Используйте event.detail вместо второго аргумента.`,
					);
				}
				// Вызываем оригинальный обработчик
				handler.call( this, e, e.detail );
			};
			return originalOn.call( this, event, selector, data, wrappedHandler );
		}

		// Возвращаем оригинальный метод для других событий
		return originalOn.apply( this, arguments );
	};

	// Патчим $.fn.trigger
	$.fn.trigger = function( event, data ) {
		// Если событие начинается с префикса "awooc_", используем CustomEvent
		if ( typeof event === 'string' && event.startsWith( EVENT_PREFIX ) ) {
			const customEvent = new CustomEvent( event, { detail: data } );
			document.dispatchEvent( customEvent );
			return this; // Сохраняем цепочку вызовов jQuery
		}

		// Возвращаем оригинальный метод для других событий
		return originalTrigger.apply( this, arguments );
	};

	// Подписываемся на все события с префиксом "awooc_"
	document.addEventListener(
		EVENT_PREFIX, // Используем префикс как тип события
		function( event ) {
			// Проверяем, начинается ли тип события с префикса
			if ( event.type.startsWith( EVENT_PREFIX ) ) {
				// Передаем событие в jQuery
				$( document ).triggerHandler( event.type, event.detail );
			}
		},
		true, // Используем capture: true для перехвата событий на этапе захвата
	);
}
