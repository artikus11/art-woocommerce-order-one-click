/* global awooc_scripts_settings, awooc_scripts_ajax, awooc_scripts_translate */

/**
 * @typedef {Object} awooc_scripts_ajax - Общий объект с ссылкой на обработчик.
 * @property {string} url - Ссылка на AJAX обработчик.
 * @property {string} nonce - Одноразовое число.
 */

/**
 * @typedef {Object} awooc_scripts_translate - Объект с переводами строк.
 * @property {string} product_qty - Количество продукта.
 * @property {string} title - Название продукта.
 * @property {string} price - Цена продукта.
 * @property {string} sku - Артикул продукта.
 * @property {string} formatted_sum - Сумма заказа.
 * @property {string} attributes_list - Список атрибутов продукта.
 * @property {string} product_data_title - Заголовок информации о выбранном продукте.
 * @property {string} product_link - Ссылка на продукт.
 * @property {string} title_close - Текст для закрытия окна.
 */

/**
 * @typedef {Object} PopupCSS - Стили для всплывающего окна.
 * @property {string} width - Ширина окна.
 * @property {string} maxWidth - Максимальная ширина.
 * @property {string} maxHeight - Максимальная высота.
 * @property {string} top - Отступ сверху.
 * @property {string} left - Отступ слева.
 * @property {string} border - Ширина границы.
 * @property {string} borderRadius - Радиус скругления углов.
 * @property {string} cursor - Курсор.
 * @property {string} overflowY - Переполнение по вертикали.
 * @property {string} boxShadow - Тень окна.
 * @property {number} zIndex - Z-индекс окна.
 * @property {string} transform - Трансформация окна.
 * @property {string} overscroll-behavior - Поведение при скролле.
 */

/**
 * @typedef {Object} PopupOverlay - Стили оверлея.
 * @property {number} zIndex - Z-индекс оверлея.
 * @property {string} backgroundColor - Цвет фона оверлея.
 * @property {number} opacity - Прозрачность оверлея.
 * @property {string} cursor - Курсор при наведении.
 */

/**
 * @typedef {Object} PopupSettings - Настройки всплывающего окна.
 * @property {number} mailsent_timeout - Время ожидания после успешной отправки письма (в мс).
 * @property {number} invalid_timeout - Время ожидания при ошибке валидации (в мс).
 * @property {number} cf7_form_id - ID выбранной формы Contact Form 7.
 * @property {string} price_decimal_sep - Десятичный разделитель в цене.
 * @property {number} price_num_decimals - Количество знаков после запятой в цене.
 * @property {string} price_thousand_sep - Разделитель тысяч в цене.
 * @property {PopupCSS} css - Стили для всплывающего окна.
 * @property {PopupOverlay} overlay - Стили оверлея.
 * @property {number} fadeIn - Время анимации появления (в мс).
 * @property {number} fadeOut - Время анимации исчезновения (в мс).
 * @property {boolean} focusInput - Автоматический фокус на поле ввода.
 */

/**
 * @typedef {Object} awooc_scripts_settings - Основной объект настроек.
 * @property {string} mode - Режим работы плагина.
 * @property {string} template - Шаблон всплывающего окна.
 * @property {string} custom_label - Произвольная надпись на кнопке.
 * @property {PopupSettings} popup - Настройки всплывающего окна.
 */

/**
 * @module awooc_scripts
 * @exports settings
 * @exports ajax
 * @exports translate
 */

export const settings = awooc_scripts_settings;
export const ajax = awooc_scripts_ajax;
export const translate = awooc_scripts_translate;
