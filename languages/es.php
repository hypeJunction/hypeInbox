<?php

$spanish = array (
    'admin:inbox:message_types' => 'Tipos de Mensaje',
    'admin:inbox' => 'Configuración de Bandeja de Entrada',
    'item:object:message:create' => 'Agregar nuevo Tipo de mensaje',
    'item:object:message:setting:multiple' => 'Permitir múltiples destinatarios',
    'item:object:message:setting:attachments' => 'Permitir archivos adjuntos',
    'item:object:message:setting:persistent' => 'Hacer persistente (no puede ser eliminado por el Destinatario)',
    'item:object:message:setting:no_subject' => 'Deshabilitar "Asunto del mensaje"',
    'item:object:message:setting:policy' => 'Política de Comunicación',
    'item:object:message:all' => 'Todos los Mensajes',
    'inbox:admin:import' => 'Importar mensajes viejos',
    'inbox:admin:import_stats' => 'Existen %s mensajes que no tienen la información necesaria para ser compatibles con hypeInbox',
    'inbox:admin:import_start' => 'Comenzar la Importación',
    'inbox:admin:import_complete' => 'Importación completada',
    'inbox:user_type:all' => 'Cualquier usuario',
    'inbox:user_type:admin' => 'Administrador',
    'inbox:user_type:editor' => 'Editor',
    'inbox:user_type:supervisor' => 'Supervisor',
    'inbox:user_type:observer' => 'Observador',
    'item:object:message:label:singular' => 'Etiqueta (singular)',
    'item:object:message:label:plural' => 'Etiqueta (plural)',
    'inbox:send' => 'Enviado %s',
    'inbox:sender' => 'Remitente',
    'inbox:relationship' => 'Relación con el Destinatario',
    'inbox:recipient' => 'Destinatario',
    'inbox:inverse_relationship' => 'Relación invertida',
    'inbox:group_relationship' => 'Relación entre el Remitente y el Grupo a cual el Destinatario pertenece',
    'inbox' => 'Mensajes',
    'inbox:all' => 'Todos los Mensajes',
    'inbox:inbox' => 'Bandeja de Entrada',
    'inbox:sent' => 'Mensajes enviados',
    'inbox:compose' => 'Redactar',
    'inbox:compose:message_type' => 'Redactar %s',
    'inbox:reply' => 'Responder',
    'inbox:reply:message_type' => 'Responder a %s',
    'inbox:reply:prefix' => 'Re:',
    'inbox:message_type' => '%s',
    'inbox:message_type:sent' => 'Enviado %s',
    'inbox:conversation:user' => 'Conversación con %s',
    'inbox:conversation:group' => 'Conversación en Grupo',
    'inbox:usersettings:grouping' => 'Agrupar mensajes recibidos por Remitente',
    'inbox:group' => 'Agrupar',
    'inbox:dontgroup' => 'No agrupar',
    'inbox:message_not_found' => 'Mensaje no encontrado',
    'inbox:untitled' => 'Sin Título',
    'inbox:me' => 'yo',
    'inbox:recipients:others' => '%s otros',
    'inbox:thread' => 'Ver los %s mensajes en esta conversación',
    'inbox:thread:count' => '%s mensajes',
    'inbox:thread:unread' => '%s nuevos',
    'inbox:thread:new' => 'Nuevo mensaje',
    'inbox:thread:participants' => 'Miembros en esta conversación',
    'inbox:attachments:count' => '%s archivos adjuntos',
    'inbox:message' => 'Mensaje: %s',
    'inbox:conversation' => 'Conversación entre Tú y %s',
    'inbox:nomessages' => 'No hay nuevos mensajes en esta conversación',
    'inbox:load:before' => 'Mostrar %s mensajes anteriores',
    'inbox:load:after' => 'Mostrar %s mensajes siguientes',
    'inbox:delete' => 'Borrar',
    'inbox:markread' => 'Marcar como Leído',
    'inbox:markunread' => 'Marcar como No Leído',
    'inbox:delete:success' => '%s mensajes eliminados con éxito',
    'inbox:delete:success:single' => 'Mensaje eliminado con éxito',
    'inbox:delete:error' => 'Los mensajes no pudieron eliminarse',
    'inbox:delete:inbox:confirm' => '¿Estás seguro de que deseas eliminar todos los mensajes seleccionados?',
    'inbox:delete:thread:confirm' => '¿Estás seguro de que deseas eliminar todos los mensajes en esta conversación?',
    'inbox:delete:message:confirm' => '¿Estás seguro de que deseas eliminar este mensaje?',
    'inbox:markread:success' => '%s mensajes marcados como Leídos',
    'inbox:markread:success:single' => 'El Mensaje fue marcado como Leído',
    'inbox:markread:error' => 'Los mensajes no pudieron ser marcados como Leídos',
    'inbox:markunread:success' => '%s mensajes marcados como No Leídos',
    'inbox:markunread:success:single' => 'Mensaje marcado como No Leído',
    'inbox:markunread:error' => 'Los mensajes no pudieron ser marcados como No Leídos',
    'inbox:error:notfound' => '%s mensajes no pudieron ser encontrados',
    'inbox:error:persistent' => '%s mensajes no pudieron ser Eliminados debido a su configuración de sólo lectura',
    'inbox:error:unknown' => '%s mensajes no pudieron ser eliminados por un error desconocido',
    'inbox:send:success' => 'Mensaje enviado con éxito',
    'inbox:send:error:no_recipients' => 'Ningún Destinatario seleccionado',
    'inbox:send:error:no_body' => 'Necesita ingresar un mensaje',
    'inbox:send:error:generic' => 'Ocurrió un error inesperado al enviar el mensaje',
    'inbox:user:unknown' => 'Desconocido',
    'inbox:form:toggle_all' => 'Marcar todos',
    'inbox:message:recipient' => 'Destinatario',
    'inbox:message:recipients' => 'Destinatarios',
    'inbox:message:subject' => 'Asunto',
    'inbox:message:body' => 'Mensaje',
    'inbox:message:attachments' => 'Archivos adjuntos',
    'inbox:message:attachments:add' => 'Agregar archivos adjuntos',
    'inbox:message:send' => 'Enviar',
    'inbox:notification:subject' => 'Tienes un nuevo %s',
    'inbox:notification:body' => 'Tienes un nuevo %s de parte de %s que dice:

%s

Para ver tus mensajes haz click aquí:
%s

Para enviar un mensaje a %s haz click aquí:
%s

Por favor, no responda este email.',
    'item:object:message:name' => 'Nombre único para este Tipo de Mensaje',
    'item:object:message:setting:policy:help' => 'Especifica una serie de usuarios entre los cuales esta comunicación puede existir.

- Los campos de "Remitente" y "Destinatario" especifican los Tipos de usuarios (basados en sus "Roles" en el sitio).

- "Relación con el Destinatario" especifica el tipo de relación que debe existir entre el Remitente y el Destinatario para que la comunicación sea permitida (ej: el Remitente debe ser Amigo del Destinatario).

- "Relación invertida" especifica que el tipo de relación debe ser invertida (ej: el Destinatario debe ser Amigo del Remitente para que el Remitente pueda enviar el mensaje al Destinatario)

- "Relación entre el Remitente y el Grupo" crea un nuevo nivel de filtro en donde:
1) El Destinatario debe ser miembro de un Grupo y,
2) El Remitente debe tener alguna relación con el Grupo (ej: especificando "Miembro" indiaría que este tipo de comunicación puede ocurrir entre miembros del mismo grupo)',
);

add_translation("es", $spanish);

return $spanish;