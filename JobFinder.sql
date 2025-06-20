-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.2
-- Время создания: Июн 20 2025 г., 16:59
-- Версия сервера: 8.2.0
-- Версия PHP: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `JobFinder`
--

-- --------------------------------------------------------

--
-- Структура таблицы `job`
--

CREATE TABLE `job` (
  `id` int NOT NULL,
  `profession` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `salary_from` int DEFAULT NULL,
  `salary_to` int DEFAULT NULL,
  `salary_currency` varchar(3) DEFAULT 'RUB',
  `salary_gross` tinyint(1) DEFAULT '1' COMMENT 'TRUE - зарплата до вычета налогов, FALSE - после',
  `location` varchar(100) DEFAULT NULL COMMENT 'Город/регион работы',
  `remote_available` tinyint(1) DEFAULT '0' COMMENT 'Возможность удалённой работы',
  `experience` varchar(50) DEFAULT NULL COMMENT 'Требуемый опыт работы',
  `employment_type` varchar(50) DEFAULT NULL COMMENT 'Тип занятости (полная, частичная и т.д.)',
  `work_schedule` varchar(50) DEFAULT NULL COMMENT 'График работы',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `job`
--

INSERT INTO `job` (`id`, `profession`, `description`, `salary_from`, `salary_to`, `salary_currency`, `salary_gross`, `location`, `remote_available`, `experience`, `employment_type`, `work_schedule`, `created_at`) VALUES
(1, 'Хуесос', 'Нужен только с именем Никита', 0, 300, 'RUB', 1, 'Салон Нирвана Красногорск', 0, 'Не требуется', 'Полная', '7/0', '2025-06-20 11:54:39');

-- --------------------------------------------------------

--
-- Структура таблицы `response`
--

CREATE TABLE `response` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_job` int NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Отклик',
  `date_response` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `response`
--

INSERT INTO `response` (`id`, `id_user`, `id_job`, `status`, `date_response`) VALUES
(1, 1, 1, 'Отклик', '2025-06-20 13:49:52');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `fuulname` varchar(64) NOT NULL,
  `email` varchar(32) NOT NULL,
  `phone` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fuulname`, `email`, `phone`) VALUES
(1, 'Dhatro@yandex.ru', '$2y$10$DXGqIROhj.Zy1LnivJ8FXeYRFxWy5kNPzy7mKOfStZddIdE/.kHx6', 'Алексей', 'Dhatro@yandex.ru', '+79197258805');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `job`
--
ALTER TABLE `job`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `response`
--
ALTER TABLE `response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_job` (`id_job`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `job`
--
ALTER TABLE `job`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `response`
--
ALTER TABLE `response`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `response`
--
ALTER TABLE `response`
  ADD CONSTRAINT `response_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `response_ibfk_2` FOREIGN KEY (`id_job`) REFERENCES `job` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
