-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Авг 19 2021 г., 16:41
-- Версия сервера: 5.5.62
-- Версия PHP: 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `db_test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authors`
--

CREATE TABLE `authors` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id автора',
  `full_name` varchar(50) NOT NULL COMMENT 'фио автора',
  `img_file` varchar(20) NOT NULL COMMENT 'файл изображения аватора автора'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица содержит авторов публикаций';

--
-- Дамп данных таблицы `authors`
--

INSERT INTO `authors` (`id`, `full_name`, `img_file`) VALUES
(1, 'Иванов Иван Андреевич', '1.jpg'),
(2, 'Сергеев Сергей Сидорович', '2.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id категории',
  `name` varchar(40) NOT NULL COMMENT 'название категории',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'id родительской категории'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='таблица содержит в себе категории публикаций';

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`) VALUES
(1, 'first category', 0),
(2, 'second_category', 0),
(3, 'sub 1', 1),
(4, 'sub 2', 1),
(5, 'Брестская область', 3),
(6, 'Брест', 5),
(7, 'Название категории', 0),
(8, 'Минск', 1),
(9, 'Гомель', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `publications`
--

CREATE TABLE `publications` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'id публикации',
  `title` varchar(15) NOT NULL COMMENT 'заголовок публикации',
  `text` text NOT NULL COMMENT 'текст публикации',
  `author_id` int(10) UNSIGNED NOT NULL COMMENT 'id автора',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `img_file` varchar(20) NOT NULL COMMENT 'имя файла изображения'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='таблица содержит в себе публикации';

--
-- Дамп данных таблицы `publications`
--

INSERT INTO `publications` (`id`, `title`, `text`, `author_id`, `created_at`, `img_file`) VALUES
(1, 'Безопасность в ', 'saass', 1, '2021-08-18 18:29:41', ''),
(2, 'Ремиссия', 'Текст описание, что такое ремиссия', 1, '2021-08-19 10:55:02', ''),
(3, 'Рефлексия', 'Текст о том, что такое рефлексия', 1, '2021-08-19 10:55:14', ''),
(4, 'Минск', 'Текст о городе Минске', 1, '2021-08-19 10:55:24', ''),
(5, 'Город Брест', 'Текст о городе Бресте', 2, '2021-08-19 10:55:38', ''),
(6, 'Витебск', 'Текст о городе Витебске', 2, '2021-08-19 10:55:47', ''),
(7, 'Заголовок', 'Текст публикации', 1, '2021-08-19 09:31:04', '7.jpg'),
(8, 'Кобрин', 'Текст публикации', 1, '2021-08-19 13:08:39', '8.jpg'),
(12, 'Гродно', 'Текст публикации о Гродно', 1, '2021-08-19 13:07:39', '');

-- --------------------------------------------------------

--
-- Структура таблицы `publications_categories`
--

CREATE TABLE `publications_categories` (
  `publication_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='таблица содержит связи публикаций с категориями';

--
-- Дамп данных таблицы `publications_categories`
--

INSERT INTO `publications_categories` (`publication_id`, `category_id`) VALUES
(1, 1),
(1, 2),
(12, 5),
(12, 4),
(12, 9),
(12, 2);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `publications`
--
ALTER TABLE `publications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at` (`created_at`)
  ADD KEY `author` (`author_id`);

--
-- Индексы таблицы `publications_categories`
--
ALTER TABLE `publications_categories`
  ADD KEY `publication` (`publication_id`),
  ADD KEY `category` (`category_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id автора', AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id категории', AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `publications`
--
ALTER TABLE `publications`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id публикации', AUTO_INCREMENT=13;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `publications_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`);

--
-- Ограничения внешнего ключа таблицы `publications_categories`
--
ALTER TABLE `publications_categories`
  ADD CONSTRAINT `publications_categories_ibfk_1` FOREIGN KEY (`publication_id`) REFERENCES `publications` (`id`),
  ADD CONSTRAINT `publications_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
