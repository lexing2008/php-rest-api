Необходимо создать сервис для создания публикаций с помощью REST API.

- Спроектировать базу данных MySql или MongoDB
- Запросы к базе данных должны быть оптимизированы
- Заполнить базу тестовыми значениям
- PHP 7.4+
- Оформить код по PSR стандартам
- Запрещается использовать фреймворки
- Использование ЧПУ вместо параметров в ссылке при оценке является плюсом
- Валидация полей при оценке является плюсом

BACK-END часть Список API-запросов:

1) создавать публикацию
2) создавать автора
3) создать категорию
4) вывести публикацию по id
5) вывести все публикации
6) вывести все публикации по определённой категории
7) вывести все публикации по автору
8) поиск (по названию, автору)

Блог будет содержать 3 объекта:

1. Объект «Публикация»:

- заголовок (15 символов)

- текст (без ограничений)

- дата создания (timestamp)

- изображение (только .jpg)

Автор может быть только 1. Публикация может относиться к нескольким категориям.

2. Объект «Автор»:

- фио (50 символов)

- аватар автора (только .jpg)

3. Объект «Категория»:

- название категории (40 символов)

- родительская категория

Уровень вложенности категорий - неограничен.

FRONT-END часть

C помощью HTML + JS + CSS использовать сервис для всех API запросов. Интерфейс должен включать:

- Блок “Категории”

- Блок “Авторы”

- Блок “Публикации”

- Блок “Поиск" (AJAX)

Дизайн не оценивается
