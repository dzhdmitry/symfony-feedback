Feedback
========

Feedback Symfony 3 app

## Структура проекта

Исходный код находится в `/src` и `/app/Resources`.

* `/src/AppBundle/Controller` - контроллеры
* `/src/AppBundle/Entity` - модели
* `/src/AppBundle/Resources/views` и `/app/Resources` (кроме public) - шаблоны
* `/tests` - тесты
* `/app/Resources/public/js` - javascript
* `/app/Resources/public/css` - стили

## Детали

* Размер файла ограничен 5 МБ
* Отметкой "изменен администратором" отмечаются только измененные сообщения

## Использованные технологии

Проект сделан на Symfony 3.1 и Doctrine с использованием бандлов:

* gedmo/doctrine-extensions
* friendsofsymfony/user-bundle
* liip/functional-test-bundle
* twig/extensions

На фронтенде используются:
* jQuery
* Bootstrap
* Backbone
* bootstrap-notify

## Тестирование

Выполнить:

* `php bin/console doctrine:database:create --env=test` (Создаст тестовую БД)
* `php bin/console doctrine:schema:create --env=test` (Создаст таблицы в БД)
* `php bin/console server:start --env=test` (Запустит тестовый сервер)
* `phpunit`
* `php bin/console server:stop`

## License

Licensed under the [MIT license](https://github.com/dzhdmitry/dx/blob/master/LICENSE).
