<?php

$spanish = array(

	'admin:inbox' => 'Configuración de la bandeja de entrada',
	'admin:inbox:message_types' => 'Tipos de mensajes',

	'item:object:message:create' => 'Añadir un tipo de mensaje',
	'item:object:message:name' => 'Nombre del tipo (único)',
	
	'item:object:message:setting:multiple' => 'Permitir múltiples destinatarios',
	'item:object:message:setting:attachments' => 'Permitir adjuntar archivos',
	'item:object:message:setting:persistent' => 'Hacer persistente (no puede ser borrado por el usuario)',
	'item:object:message:setting:no_subject' => 'Desactivar la línea de asunto',
	'item:object:message:setting:policy' => 'Política de comunicación',
	'item:object:message:setting:policy:help' => 'Especifica grupos de usuarios entre los que puede haber comunicación
- Los campos "Remitente" y "Destinatario" indican tipos de usuarios (basados en los roles del sitio)
- La "relación con el destinatario" indica el tipo de relación que debe existir entre el destinatario y el remitente para que esta comunicación se permita (p.e. el remitente debe ser amigo del destinatario).
- La "relación inversa" indica que la relación se debe invertir (p.e. el destinatario debe ser amigo del remitente para que el remitente pueda contactar con el destinatario)
- La "relación del remitente con el grupo" crea un nivel adicional de filtrado donde 1) el destinatario debe ser miembro del grupo y 2) el remitente debe tener una relación específica con ese grupo (p.e. fijarlo a "miembro" indicaría que este tipo de comunicación solo puede suceder entre miembros del mismo grupo)',

	'item:object:message:all' => 'Todos los mensajes',

	'inbox:admin:import' => 'Importar mensajes antiguos',
	'inbox:admin:import_stats' => '%s mensajes no contienen metadatos que se requieren para la compatibilidad con hypeInbox',
	'inbox:admin:import_start' => 'Empezar la importación',
	'inbox:admin:import_complete' => 'La importación ha terminado',
	
	'inbox:user_type:all' => 'Cualquier usuario',
	'inbox:user_type:admin' => 'Administrador',
	'inbox:user_type:editor' => 'Editor',
	'inbox:user_type:supervisor' => 'Supervisor',
	'inbox:user_type:observer' => 'Observador',

	'item:object:message:label:singular' => 'Etiqueta (singular)',
	'item:object:message:label:plural' => 'Etiqueta (plural)',

	'inbox:send' => 'Enviar %s',
	'inbox:sender' => 'Remitente',
	'inbox:relationship' => 'Relación con el destinatario',
	'inbox:recipient' => 'Destinatario',
	'inbox:inverse_relationship' => 'Relación inversa',
	'inbox:relationship' => 'Relación con el destinatario',
	'inbox:group_relationship' => 'Relación del remitente con el grupo al que pertenece el destinatario',

	'inbox' => 'Mensajes',
	'inbox:all' => 'Todos los mensajes',
	'inbox:inbox' => 'Bandeja de entrada',
	'inbox:sent' => 'Mensajes enviados',
	'inbox:compose' => 'Escribir',
	'inbox:compose:message_type' => 'Escribir un %s',
	'inbox:reply' => 'Contestar',
	'inbox:reply:message_type' => 'Contestar a un %s',
	'inbox:reply:prefix' => 'Sobre:',
	'inbox:message_type' => '%s',
	'inbox:message_type:sent' => 'Enviar %s',

	'inbox:conversation:user' => 'Conversación con %s',
	'inbox:conversation:group' => 'Conversación de grupo',
	
	'inbox:usersettings:grouping' => 'Mensajes en la bandeja de entrada del grupo por remitente',
	'inbox:group' => 'Grupo',
	'inbox:dontgroup' => 'No agrupar',

	'inbox:message_not_found' => 'No se ha encontrado el mensaje',
	
	'inbox:untitled' => 'Sin título',
	'inbox:me' => 'yo',
	'inbox:recipients:others' => '%s otros',
	
	'inbox:thread' => 'Ver los %s mensajes en este hilo',
	'inbox:thread:count' => '%s mensajes',
	'inbox:thread:unread' => '%s nuevo',
	'inbox:thread:new' => 'Nuevo mensaje',
	'inbox:thread:participants' => 'Miembros en este hilo',

	'inbox:attachments:count' => '%s adjuntos',
	
	'inbox:message' => 'Mensaje: %s',
	'inbox:conversation' => 'Conversación entre %s y tu',

	'inbox:nomessages' => 'No hay mensajes en este hilo',

	'inbox:load:before' => 'Mostrar los %s mensajes anteriores',
	'inbox:load:after' => 'Mostrar los %s siguientes mensajes',

	'inbox:delete' => 'Borrar',
	'inbox:markread' => 'Marcar como leído',
	'inbox:markunread' => 'Marcar como no leído',
	
	'inbox:delete:success' => 'Se han borrado correctamente %s mensajes',
	'inbox:delete:success:single' => 'El mensaje se ha borrado correctamente',
	'inbox:delete:error' => 'Los mensajes no se han podido borrar',
	'inbox:delete:inbox:confirm' => '¿Está seguro de querer borrar todos los mensajes seleccionados?',
	'inbox:delete:thread:confirm' => '¿Está seguro de querer todos los mensajes de este hilo?',
	'inbox:delete:message:confirm' => '¿Está seguro de querer borrar este mensaje?',
	
	'inbox:markread:success' => 'Se han marcado %s mensajes como leído',
	'inbox:markread:success:single' => 'El mensaje se ha marcado como leído',
	'inbox:markread:error' => 'El mensaje no se ha podido marcar como leído',
	
	'inbox:markunread:success' => 'Se han marcado %s mensajes como leídos',
	'inbox:markunread:success:single' => 'El mensaje se ha marcado como no leído',
	'inbox:markunread:error' => 'No se han podido marcar los mensajes como no leídos',
	
	'inbox:error:notfound' => 'No se han encontrado %s mensajes',
	'inbox:error:persistent' => 'No se han podido borrar %s mensajes por ser de solo lectura',
	'inbox:error:unknown' => 'No se han podido borrar %s mensajes por un error desconocido',

	'inbox:send:success' => 'El mensaje se ha enviado correctamente',
	'inbox:send:error:no_recipients' => 'No se han seleccionado destinatarios',
	'inbox:send:error:no_body' => 'Se necesita un cuerpo de mensaje',
	'inbox:send:error:generic' => 'Ha habido un error desconocido enviando el mensaje',
	
	'inbox:user:unknown' => 'Desconocido',
	'inbox:form:toggle_all' => 'Marcar todo',
	
	'inbox:message:recipient' => 'Destinatario',
	'inbox:message:recipients' => 'Destinatarios',
	'inbox:message:subject' => 'Asunto',
	'inbox:message:body' => 'Mensaje',
	'inbox:message:attachments' => 'Adjuntos',
	'inbox:message:attachments:add' => 'Añadir adjuntos',
	'inbox:message:send' => 'Enviar',
	
	'inbox:notification:subject' => 'Tiene un nuevo %s',
	'inbox:notification:body' => "Tiene un nuevo %s de %s. Pone:

%s

Para ver sus mensajes, haga clic aquí:

%s

Para enviar un mensaje a %s, haga clic aquí:

%s

Por favor no responda a este correo.",
);

add_translation("es", $spanish);
