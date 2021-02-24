# Art WooCommerce Order One Click

Плагин под WooCommerce.  Включает четыре режима: каталог, заказать, предзаказ и специальный режим работы с отсутвием цен и запасов

Плагин работает только с плагином Contact Form 7

[Полное описание](https://wpruse.ru/my-plugins/order-one-click/)

[Скачать плагин](https://github.com/artikus11/art-woo-order-one-click/releases)

[Плагин в репозитории WordPress](https://wordpress.org/plugins/art-woocommerce-order-one-click/)

## Changelog

### = 2.4.0 =
* Обновлено - инициализация форм для CF7 5.4
* Обновлено - рефакторинг кода
* Исправлено - ошибка вывода индивидуальныз атрибутов
* Исправлено - вывод суммы заказа в окне и письме

### = 2.3.9.1 =
* Исправлено - поведение кнопки Купить в специальном режиме

### = 2.3.9 =
* Исправлено - исправление html статуса, если товара нет в наличии
* Исправлено - стили скытия шататной кнопки Купить

### = 2.3.8 =
* Исправлено - стили скрытия кнопки
* Исправлено - стили прелоадера
* Добавлено - уникальный идентификатор кнопки

### = 2.3.7 =
* Добавлено - дополнительная настройка для глобального подклчени скриптов и стилей
* Обновлено - переподключение скриптов кнопки
* Обновлено - стили прелоадера кнопки
* Обновлено - переименование классов прелоада
* Обновлено - html кнопки изменен c <a> на <button>
* Исправлено - вывод количества на страницах каталога
* Исправлено - поведение окна при загрузке

### = 2.3.6 =
* Добавлено - фильтр awooc_data_ajax в обработчике ajax
* Добавлено - объект для настроек внешнего вида окна
* Добавлено - фильтр awooc_popup_setting в wp_localize_script для настроек окна
* Добавлено - сообщение в консоли, если на странице нет объекта wpcf7
* Добавлено - создание отдельного файла для подключения стилей
* Добавлено - перенос функционала подключения стилей в отдельный файл
* Добавлено - новые классы в элементах всплывающего окна
* Добавлено - фильтр awooc_mode_classes_button для добавления классов в кнопку
* Обновлено - переименование основного класса
* Обновлено - выключен фокус в поле формы при открытии окна
* Обновлено - декодирование заголовков при выводе
* Обновлено - символ тире в письме
* Обновлено - замена класса для активации ajax
* Обновлено - удалено затемнение на кнопке при активации ajax
* Обновлено - разнесены создание объектов wp_localize_script по типам (ajax, translate, settings)
* Обновлено - подключение скриптов и стилей только при подключении кнопки
* Обновлено - инициализция окна c id на класс и скрытие штатными методами jquery
* Обновлено - форматирование и сжатие скриптов и стилей
* Обновлено - кнопки для разных режимов
* Обновлено - вывод классов на кнопке
* Исправлено - ошибка при первой активации плагина
* Исправлено - если просто товар, то атрибуты не выводятся в окне
* Исправлено - стили всплывающего окна
* Исправлено - ошибка создания формы при активации окна
* Исправлено - поведение кнопки в режиме предзаказа и отсутсвие стоков
* Исправлено - слипание кнопок в спецрежиме
* Исправлено - отключение кнопко в похожих при спецрежиме если нет запасов
* Рефакторинг - методы класса AWOOC_Front_End

### = 2.3.5 =
* Исправлено - правки в js, убрана проверка на объект WC

### = 2.3.4 =
* Исправлено - правки в js

### = 2.3.3 =
* Исправлено - ошибка при использовании маски телефона

### = 2.3.2 =
* Исправлено - сбор данных и изменения в триггере awooc_mail_sent_trigger

### = 2.3.1 =
* Исправлено - фатальная ошибка похожих товаров в режиме каталога
* Исправлено - фатальная ошибка при активации, если не активен CF7
* Обновлено - рефакторинг кода в js
* Обновлено - вынос установки формы при активации в отдельный файл
* Обновлено - проверка активации кнопки
* Обновлено - поддержка новых версий WC
* Добавлено - awooc_popup_open_trigger триггер при открытии окна
* Добавлено - awooc_popup_close_trigger триггер при закрытии окна
* Добавлено - awooc_mail_sent_trigger триггер при отправке формы
* Добавлено - awooc_mail_invalid_trigger триггер при ошибке в момент отправки формы

### = 2.3.0 =
* Обновлено - получение номера заказа вместо id
* Обновлено - кнопка появляется в режиме каталога даже если в вариациях нет цен
* Добавлено - новая настройка, если включить настройки не удаляются при удалении плагина
* Добавлено - метабокс в товарах для отключения функцинала плагина на конкретном товаре
* Добавлено - создание формы в CF7 при первой активации плагина

### = 2.2.9 =
* Исправлено - ошибка при обновлении настроек

### = 2.2.8 =
* Добавлено - фильтр `awooc_selected_form_id` для отлавливания ID формы

### = 2.2.7 =
* Исправлено - небольшие правки по коду

### = 2.2.6 =
* Добавлено - дополнительные настройки для вывода инфоблоков
* Добавлено - поддержка Polylang и WPML
* Добавлено - изменение заголовка письма перед отправкой с добвление номера заказа

### = 2.2.5 =
* Исправлено - поведение кнопки Купить в Похожих товарах
* Добавлено - если включено страничное кеширование, то проверка нонсы не проиходит

### = 2.2.4 =
* Исправлено - исправлено поведение формы если нет элементов в окне

### = 2.2.3 =
* Исправлено - поведение кнопки Купить в специальном режиме в вариативных товарах
* Исправлено - поведение кнопки Заказать в специальном режиме на страницах архивов
* Исправлено - стили формы и окна на мобильных устройствах

### = 2.2.2 =
* Исправлено - работа кнопки заказать в режиме каталога
* Исправлено - стили загружаемой формы

### = 2.2.1 =
* Исправлено - скрытие кнопки в режиме каталога
* Изменено - описание настроек режимов

### = 2.2.0 =
* Исправлено - поддержка плагина WPBakery Page Builder
* Исправлено - рефакторинг кода
* Добавлено - дополнительный режим Нет цен и запасов для появления кнопки Заказать
* Добавлено - фильтр `awooc_button_label` для изменения надписи на кнопке
* Добавлено - новая настройка Произвольная надпись для изменения надписи на кнопке в режими Нет цен и запасов
* Добавлено - минификация стилей и скриптов

### = 2.1.4 =
* Добавлено - стили колонок в aдминке
* Добавлено - новое произвольное поле в настройках (подготовка к экстра)
* Добавлено - вспомогательная функция вывода класса в зависимсоти от режима работы
* Изменено - стили на фронте

### = 2.1.3 =
* Исправлено - стили колонок в окне
* Изменено - подлючение основного скрипта
* Изменено - поведение кнопки

### = 2.1.2 =
* Исправлено - поведение поведение поля количество при закрытии окна

### = 2.1.1 =
* Добавлено - фильтр `awooc_order_address_arg` для добавления данных в заказ
* Добавлено - хук `awooc_after_created_order` для добавления данных в заказ
* Исправлено - поведение прелоадера
* Изменено - запись адреса при создании заказа теперь пишется и в billing, и в shipping

### = 2.1.0 =
* Добавлено - опция отключения показа количества в окне
* Добавлено - вывод категорий товара в письме
* Исправлено - создание заказов с любой формы на странице
* Исправлено - поведение окна на мобильных
* Исправлено - поведение окна при отключении все элементов, форма растягивается на всю ширину
* Изменено - при отключении всех элементов, все нужные данные отправляются в письмо

### = 2.0.0 =
* Добавлено - локализация, теперь можно переводить на разные языки
* Добавлено - проверка на наличие/отсутсвие ID товара при открытие окна
* Добавлено - появление кнопки заказать, если в вариативных товарах нет цены
* Исправлено - ошибка работы окна на простых товарах
* Исправлено - предупреждения и ошибки
* Исправлено - форма вставки поля в CF7
* Исправлено - ошибка вывода кнопки Купить если нет цены на простых товарах
* Исправлено - работа кнопки в Быстром просмотре
* Изменено - проведена проверка и рефакторинг кода
* Изменено - изменена система проверки на обязательные плагины
* Удалено - фильтр `awooc_html_add_to_cart`

### = 1.8.10 =
* Исправлено - скрытие окна на всехх страницах сайта
* Исправлено - очистка данных при выводе окна
* Изменено - обновление readme

### = 1.8.9 =
* Исправлено - ошибка определения типа продукта
* Изменено - обновление readme
   
### = 1.8.8 =
* Исправлено - обзод блокировки скрипта при работе плагина Popup Maker
* Изменено - обновление readme для добавления в репозиторий WP

### = 1.8.7 =
* Исправлено - скрипт вызова окна

### = 1.8.6 =
* Исправлено - ошибка отправки формы, если на странице есть еще формы
* Изменено - внешний вид вывода атрибутов
* Добавлено - ограничение загрузки скриптов и стилей плагина только на страницах WooCommerce
* Добавлено - работа сплывающего окна в режиме Быстрого просмотра

### = 1.8.5 =
* Исправлено - ошибка REST API при подключении плагина
* Изменено - сброс настроек при деактивации плагина на сброс при деинсталяции плагина
* Добавлено - проверка на выбор атрибутов в вариативных товарах, аналокично штатной кнопке, если атрибуты не выбраны то кнопка не работает

### = 1.8.4 =
* Исправлено - вывод в заголовке хмтл-сущьностей
* Добавлено - поддержка плагина Contact Form 7 – Phone mask field
* Добавлено - комментарии в js файле

### = 1.8.3 =
* Добавлено - скрытие +/- для поля количество в режиме каталога и предзаказа

### = 1.8.2 =
* Исправлено - заказы созаются со статусом "Ожидание заказа"
* Исправлено - вывод картинки для вариаций, если картинки нет, то выводится родительская
* Удалено - настройка отключения отправки писем клиенту при создании заказа

### = 1.8.1 =
* Исправлено получение данных с хтмл тегами. Теперь приходят только чистые данные
* Исправлен вывод формы во всплывающем окне, если отключен вывод данных
* Изменено включение всплывающего окна. Теперь окно загружается сразу с данными
* Добавлена загрузка формы во всплывающем окне через ajax

### = 1.8.0 =
* Переписан код
* Исправлена ошибка видимости окна в подвале
* Исправлено отправка писем при создании заказа
* Добавлена поддержка WPCS
* Добавлено отключение отправки писем пользователю при создании заказа
* Добавлено передача количества в заказ
* Добавлен хук `awooc_before_button` для добавления чего-нибудь перед кнопкой
* Добавлен хук `awooc_after_button` для добавления чего-нибудь после кнопки
* Добавлен хук `awooc_attributes_button` для добавления аттрибутов внутри кнопки
* Добавлен хук `awooc_after_mail_send` для ловли отправки письма и создания заказа
* Удален хук `awooc_popup_before_image`
* Удален хук `awooc_popup_after_image`


### = 1.7.0 =
* Добавлен фильтр `awooc_popup_attr_label` для возможности изменения надписи перед атрибутами в окне
* Добавлена вывод и отправка выбранного количества товаров
* Удалены хуки `awooc_popup_title_html_tag_open`, `awooc_popup_title_html_tag_close`
* Удалены хуки `awooc_popup_image_width`, `awooc_popup_image_heigh`
* Удалены хуки `awooc_popup_before_price`, `awooc_popup_after_price`
* Удалены хуки `awooc_popup_before_sku`, `awooc_popup_after_sku`
* Удалены хуки `awooc_popup_before_attr`, `awooc_popup_after_attr`
* Исправлены ошибки

### = 1.6.9 =
* Переименнованы файлы
* Добавлена function_exists для возможности изменения функций
* Переписан функционал вывода всплывающего окна
* переписано получение данных в модальном окне
* Добавлено отправка ссылки на выбранный товар в письме
* Добавлен фильтр `awooc_html_add_to_cart` для возможности изменения хтмл кнопки
* Добавлен фильтр `awooc_classes_button` для возможности добавления классов к кнопке
* Добавлен фильтр `awooc_popup_title_html` для возможности изменения стилей заголовка модального окна
* Добавлен фильтр `awooc_popup_title_html_tag_open` для изменения открывающего тега заголовка модального окна
* Добавлен фильтр `awooc_popup_title_html_tag_close` для изменения закрывающего тега заголовка модального окна
* Добавлен фильтр `awooc_popup_title_html_classes` для добавления классов к заголовку модального окна
* Добавлен фильтр `awooc_popup_image_html``` для возможности изменения хтмл изображения в окне
* Добавлен фильтр `awooc_popup_image_alt` для добавления alt к изображению в окне
* Добавлен фильтр `awooc_popup_image_classes` для добавления классов к изображению в окне
* Добавлен фильтр `awooc_popup_image_width` для изменения ширины изображения в окне
* Добавлен фильтр `awooc_popup_image_heigh` для изменения высоты изображения в окне
* Добавлен хук `awooc_popup_before_image` для добавления чего-нибудь перед изображением в окне
* Добавлен хук `awooc_popup_after_image` для добавления чего-нибудь после изображением в окне
* Добавлен фильтр `awooc_popup_price_html` для возможности изменения хтмл цены в окне
* Добавлен фильтр `awooc_popup_price_label` для возможности изменения надписи перед ценой в окне
* Добавлен хук `awooc_popup_before_price``` для добавления чего-нибудь перед ценой в окне
* Добавлен хук `awooc_popup_after_price` для добавления чего-нибудь после ценой в окне
* Добавлен фильтр `awooc_popup_sku_html` для возможности изменения хтмл артикула в окне
* Добавлен фильтр `awooc_popup_sku_label` для возможности изменения надписи перед артикулом в окне
* Добавлен хук `awooc_popup_before_sku` для добавления чего-нибудь перед арикулом в окне
* Добавлен хук `awooc_popup_after_sku` для добавления чего-нибудь после артикула в окне
* Добавлен хук `awooc_popup_before_attr` для добавления чего-нибудь перед атрибутами в окне
* Добавлен хук `awooc_popup_after_attr` для добавления чего-нибудь после атрибутов в окне
* Добавлен хук `awooc_popup_before_form` для добавления чего-нибудь перед формой в окне
* Добавлен хук `awooc_popup_after_form` для добавления чего-нибудь после формой в окне
* Добавлен хук `awooc_popup_before_column`
* Добавлен хук `awooc_popup_column_left`
* Добавлен хук `awooc_popup_column_right`
* Добавлен хук `awooc_popup_after_column`
* Добавлены стили тени и овефлоу к сплывающему окну
* Изменено поведение окна при ошибке ввода полей формы
* При закрытии окна удаляется хеш из урла


### = 1.6.8 =
* Добавлен фильтр `awooc_enable_add_to_card_style` для возможности изменения стилей
* Добавлен фильтр ```awooc_disable_add_to_card_style``` для возможности изменения стилей
* Исправлены скрытия кнопки Купить в первом режиме
* Обновлен код

### = 1.6.7 =
* Добавлен фильтр ```awooc_classes_button``` для добавления классов к кнопке
* Добавлен фильтр ```awooc_thumbnail_name``` для названия миниатюры во всплывающем окне
* Переименованы файлы, для исключения конфликтов
* Исправлены стили

### = 1.6.6 =
* Добавлена проверка на версию php
* Добавлена ссылка на настройки в списке плагинов
* Добавлена ссылка на статью в описании плагина
* Изменен второй режим работы, теперь кнопка Купить работает в штатном режиме
* Исправлены ошибки стилей

### = 1.6.5 =
* Добавлено определение распродажной цены
* Исправлены ошибки стилей

### = 1.6.4 =
* Добавлена отправка цены товара в скрытом поле
* Добавлены описания строк в скрытом поле для отправки в письме
* Изменено скрытие цены
* Исправлены ошибки

### = 1.6.3 =
* Исправлены ошибки

### = 1.6.2 =
* Добавлено появление кнопки Заказать, если нет цены у товара, в режиме управления запасами
* Исправлена логика появления кнопки Заказать при управлении запасами
* Исправлены ошибки

### = 1.6.1 =
* Исправлена ошибка использования отмененной функции

### = 1.6.0 =
* Добавлена адаптивность окна
* Добавлена кнопка закрытия окна
* Добавлено отключение кнопки Купить в Похожих и Апселлах
* Добавлен функционал создания заказов
* Добавлена настройка включения/выключения созданием заказов
* Добавлены комментарии к коду
* Изменены настройки режимов работы, теперь три режима
* Изменены настройки по умолчанию при выводе элементов окна
* Исправлено скрытие кнопки Купить
* Исправлены ошибки

### = 1.5.3 =
* Исправлены ошибки

### = 1.5.2 =
* Исправлены ошибки
* Добавлено удаление опций при деинсталяции

### = 1.5.1 =
* Исправлены ошибки

### = 1.5.0 =
* Добавлена настройка управления режимом каталога
* Добавлена настройка управления отображением элементов в попап окне
* Добавлена настрока управления надписью на кнопке
* Добавлена отправка артикула
* Исправлены ошибки

### = 1.4.0 =
* Добавлена кнопка при редактировании формы Contact Form 7
* Добавлены настройки для управления формами
* Обновлены проверки на наличие плагинов
* Исправление ошибок

### = 1.3.0 =
* Обновление настроек
* Исправление ошибок

### = 1.2.0 =
* Обновление настроек

### = 1.1.0 =
* Обновление функций
* Добавление проверок
* Добавление настроек

### = 1.0.0 =
* Релиз
