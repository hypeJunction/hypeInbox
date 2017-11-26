<?php

require_once dirname(dirname(__FILE__)). '/autoloader.php';

$translations = [
	'admin:inbox' => 'Настройки',
	'admin:inbox:message_types' => 'Типы сообщений',
	'item:object:message:create' => 'Добавьте новый тип сообщения',
	'item:object:message:name' => 'Уникальное наименование',
	'item:object:message:setting:multiple' => 'Несколько получателей',
	'item:object:message:setting:attachments' => 'Вложение файлов',
	'item:object:message:setting:persistent' => 'Сделать постоянным (не может быть удален получателем)',
	'item:object:message:setting:no_subject' => 'Отключить строку для темы (Subject)',
	'item:object:message:setting:policy' => 'Политика взаимосвязи',
	'item:object:message:setting:policy:help' => 'Установки пользователей, между которыми может произойти такая взаимосвязь.
		Поля "Отправитель" и "Получатель" задают типы пользователей (на основе их "ролей" на сайте).
		"Связь с получателем" указывает на тип отношения, который должен существовать между Отправителем и Получателем, чтобы допустиить отправку сообщения (например, Отправитель должен быть другом Получателя).
		"Обратная связь" указывает на то, что тип отношения должен быть обратным (например, Получатель должен быть другом Отправителя и тогда у Отправителя будет возможность связаться с Получателем).
		"Связь отправителя с группой" создает такой дополнительный уровень фильтрации, что 1) Получатель должен быть членом группы и 2) Отправитель должен иметь определенную связь с этой группой (например, установка этого параметра в опцию "член" указывает на то, что данный тип связи может происходить только между членами одной и той же группы).
	',
	'item:object:message:all' => 'Все сообщения',
	'inbox:admin:import' => 'Импорт старых сообщений',
	'inbox:admin:import_stats' => 'В %s сообщениях отсутствует информация о метаданных, необходимых для совместимости с hypeInbox',
	'inbox:admin:import_start' => 'Начать импорт',
	'inbox:admin:import_complete' => 'Импорт завершен',
	'inbox:user_type:all' => 'Любой пользователь',
	'inbox:user_type:admin' => 'Администратор',
	'inbox:user_type:editor' => 'Редактор',
	'inbox:user_type:supervisor' => 'Руководитель',
	'inbox:user_type:observer' => 'Наблюдатель',
	'item:object:message:label:singular' => 'Ярлык (единственное число)',
	'item:object:message:label:plural' => 'Ярлык (множественное число)',
	'inbox:send' => 'Отправить %s',
	'inbox:sender' => 'Отправитель',
	'inbox:recipient' => 'Получатель',
	'inbox:inverse_relationship' => 'Обратная связь',
	'inbox:relationship' => 'Связь с получателем',
	'inbox:group_relationship' => 'Связь отправителя с группой, в которой получатель является членом',
	'inbox' => 'Сообщения',
	'inbox:all' => 'Все сообщения',
	'inbox:inbox' => 'Входящие',
	'inbox:sent' => 'Отправленные сообщения',
	'inbox:compose' => 'Написать',
	'inbox:new' => 'Новое %s',
	'inbox:compose:message_type' => 'Написать %s',
	'inbox:compose:forward' => 'Переслать %s',
	'inbox:reply' => 'Ответить',
	'inbox:reply:message_type' => 'Ответить на %s',
	'inbox:reply:prefix' => 'Re:',
	'inbox:message_type' => '%s',
	'inbox:message_type:sent' => 'Отправленные %s',
	'inbox:conversation:user' => 'Переписка с %s',
	'inbox:conversation:group' => 'Переписка группы',
	'inbox:usersettings:grouping' => 'Сгруппировать входящие сообщения отправителя',
	'inbox:group' => 'Сгруппировать',
	'inbox:dontgroup' => 'Не группировать',
	'inbox:message_not_found' => 'Сообщение не найдено',
	'inbox:untitled' => 'Без названия',
	'inbox:me' => 'я',
	'inbox:recipients:others' => '%s другие',
	'inbox:thread' => 'Посмотреть все %s сообщения в этой теме',
	'inbox:thread:count' => '%s сообщений',
	'inbox:thread:unread' => '%s новых',
	'inbox:thread:new' => 'Новое сообщение',
	'inbox:thread:participants' => 'Участники этой темы',
	'inbox:attachments:count' => '%s вложений',
	'inbox:message' => 'Сообщение: %s',
	'inbox:conversation' => 'Переписка между вами %s',
	'inbox:nomessages' => 'Нет сообщений в этой теме',
	'inbox:load:before' => 'Загрузить предыдущие сообщения',
	'inbox:load:after' => 'Загрузить новые сообщения',
	'inbox:delete' => 'Удалить',
	'inbox:forward' => 'Переслать',
	'inbox:markread' => 'Отметить как прочитанное',
	'inbox:markunread' => 'Отметить как непрочитанное',
	'inbox:delete:success' => '%s сообщений было удалено',
	'inbox:delete:success:single' => 'Сообщение было удалено',
	'inbox:delete:error' => 'Сообщение не может быть удалено',
	'inbox:delete:inbox:confirm' => 'Вы уверены, что хотите удалить все выбранные сообщения?',
	'inbox:delete:thread:confirm' => 'Вы уверены, что хотите удалить все сообщения в этой теме?',
	'inbox:delete:message:confirm' => 'Вы уверены, что хотите удалить это сообщение?',
	'inbox:markread:success' => '%s сообщений было отмечено прочитанными',
	'inbox:markread:success:single' => 'Сообщение было отмечено, как прочитанное',
	'inbox:markread:error' => 'Сообщения не могут быть отмечены, как прочитанные',
	'inbox:markunread:success' => '%s сообщений было отмечено непрочитанными',
	'inbox:markunread:success:single' => 'Сообщение было отмечено, как непрочитанное',
	'inbox:markunread:error' => 'Сообщения не могут быть отмечены, как непрочитанные',
	'inbox:error:notfound' => '%s сообщений не могут быть найдены',
	'inbox:error:persistent' => '%s сообщений не могут быть удалены из-за их установки только для чтения',
	'inbox:error:unknown' => '%s сообщений не могут быть удалены по причине неизвестной ошибки',
	'inbox:send:success' => 'Сообщение было отправлено',
	'inbox:send:error:no_recipients' => 'Не выбраны получатели',
	'inbox:send:error:no_body' => 'Вам нужно добавить текст сообщения',
	'inbox:send:error:generic' => 'Произошла неизвестная ошибка во время отправки сообщения',
	'inbox:user:unknown' => 'Неизвестный',
	'inbox:form:toggle_all' => 'Выбрать все',
	'inbox:message:recipient' => 'Получатель',
	'inbox:message:recipients' => 'Получатели',
	'inbox:message:subject' => 'Тема',
	'inbox:message:body' => 'Сообщение',
	'inbox:message:attachments' => 'Вложения',
	'inbox:message:attachments:add' => 'Прикрепить файл',
	'inbox:message:send' => 'Отправить',
	'inbox:notification:subject' => 'У вас новое %s',
	'inbox:notification:body' => "У вас новое %s от %s. Текст сообщения:


	%s


	Перейдите по ссылке, чтобы прочитать сообщение:

	%s

	Перейдите по ссылке, чтобы отправить сообщение %s:

	%s

	Не отвечайте на это письмо.",
	'inbox:empty' => 'Ваш ящик сообщений пуст',
	'inbox:settings:enable_html' => 'Разрешить HTML в сообщениях',
	'inbox:settings:enable_html:help' => 'HTML в сообщениях должен быть разрешен, если ваш сайт поддерживает исходящие HTML-письма',
	'inbox:byline' => 'От %s',
	'inbox:to' => 'Кому %s',
	'inbox:byline:thread' => 'Последнее сообщение от %s',
	'inbox:search' => 'Поиск',

	'messages:forward:byline' => 'Переадресованное сообщение от %s (изначально отправлено %s)',
	
];

$message_types = hypeInbox()->config->getMessageTypes();

// Register label translations for custom message types
foreach ($message_types as $type => $options) {
	$ruleset = hypeInbox()->config->getRuleset($type);
	$translations[$ruleset->getSingularLabel(false)] = $ruleset->getSingularLabel('ru');
	$translations[$ruleset->getPluralLabel(false)] = $ruleset->getPluralLabel('ru');
}

return $translations;