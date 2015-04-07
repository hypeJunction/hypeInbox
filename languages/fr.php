<?php

/**
 * French translations contributed by @choletj
 */
return array(
	'admin:inbox' => 'Paramètres de la messagerie',
	'admin:inbox:message_types' => 'Types de message',
	'item:object:message:create' => 'Ajouter un nouveau type de message',
	'item:object:message:name' => 'Identifiant unique',
	'item:object:message:setting:multiple' => 'Autoriser plusieurs destinataires',
	'item:object:message:setting:attachments' => 'Autoriser les pièces jointes',
	'item:object:message:setting:persistent' => 'Les messages ne pourront pas être supprimés par le destinataire',
	'item:object:message:setting:no_subject' => 'Inhiber le champ sujet',
	'item:object:message:setting:policy' => 'Règles de communication',
	'item:object:message:setting:policy:help' => 'Détermine le jeu d\'utilisateurs entre lesquels l\'envoi de message est possible.
        Les champs "Expéditeur" et "Destinataire" contiennent les rôles des utilisateurs
        "Relation avec le destinataire" contient le type de relation qui doit lier destinataire et expéditeur (L\'expéditeur doit être un contact du destinataire par exemple).
        "Relation inverse" indique si le type de relation doit être valable dans le sens inverse (Le destinataire doit être un contact de l\'expéditeur par exemple).
        "Relation du destinataire avec un groupe" impose un filtre supplémentaire pour lequel l\'expéditeur doit être membre d\'un groupe auquel appartient aussi le destinataire.
    ',
	'item:object:message:all' => 'Tous les messages',
	'inbox:admin:import' => 'Importer les anciens messages',
	'inbox:admin:import_stats' => 'Les métadatas sont manquantes pour %s messages. Ces informations sont indispensables pour des raisons de compatibilité',
	'inbox:admin:import_start' => 'Commencer l\'import',
	'inbox:admin:import_complete' => 'L\'import a été finalisé.',
	'inbox:user_type:all' => 'N\'importe quel utilisateur',
	'inbox:user_type:admin' => 'Administrateur',
	'inbox:user_type:editor' => 'Editeur',
	'inbox:user_type:supervisor' => 'Superviseu',
	'inbox:user_type:observer' => 'Observateur',
	'item:object:message:label:singular' => 'Label (au singulier)',
	'item:object:message:label:plural' => 'Label (au pluriel)',
	'inbox:send' => 'Send %s',
	'inbox:sender' => 'Expéditeur',
	'inbox:relationship' => 'Relation avec le destinataire',
	'inbox:recipient' => 'Destinataire',
	'inbox:inverse_relationship' => 'Relation inverse',
	'inbox:relationship' => 'Relation avec le destinataire',
	'inbox:group_relationship' => 'Relation de l\'expéditeur au groupe dont le destinataire est membre',
	'inbox' => 'Messages',
	'inbox:all' => 'Tous les messages',
	'inbox:inbox' => 'Boîte de réception',
	'inbox:sent' => 'Boîte d\'envoi',
	'inbox:compose' => 'Rédiger',
	'inbox:compose:message_type' => 'Rédiger un %s',
	'inbox:reply' => 'Répondre',
	'inbox:reply:message_type' => 'Répondre à un %s',
	'inbox:reply:prefix' => 'Re:',
	'inbox:message_type' => '%s',
	'inbox:message_type:sent' => 'Sent %s',
	'inbox:conversation:user' => 'Fil de discussion avec %s',
	'inbox:conversation:group' => 'Fil de discussion d\'un groupe',
	'inbox:usersettings:grouping' => 'Regrouper les messages par expéditeur',
	'inbox:group' => 'Group',
	'inbox:dontgroup' => 'Ne pas regrouper',
	'inbox:message_not_found' => 'Message non trouvé',
	'inbox:untitled' => 'Sans titre',
	'inbox:me' => 'moi',
	'inbox:recipients:others' => '%s autres',
	'inbox:thread' => 'Voir tous les %s messages dans ce fil',
	'inbox:thread:count' => '%s messages',
	'inbox:thread:unread' => '%s nouveaux',
	'inbox:thread:new' => 'Nouveau message',
	'inbox:thread:participants' => 'Membres dans ce fil',
	'inbox:attachments:count' => '%s pièces jointes',
	'inbox:message' => 'Message: %s',
	'inbox:conversation' => 'Conversation entre vous et %s',
	'inbox:nomessages' => 'Il n\'y a aucun message dans ce fil',
	'inbox:load:before' => 'Montrer les %s messages précédents',
	'inbox:load:after' => 'Montrer les %s messages suivants',
	'inbox:delete' => 'Supprimer',
	'inbox:markread' => 'Marquer comme lu',
	'inbox:markunread' => 'Marquer comme non lu',
	'inbox:delete:success' => '%s messages ont été supprimés.',
	'inbox:delete:success:single' => 'Le message a été supprimé.',
	'inbox:delete:error' => 'Les messages n\'ont pu être supprimés.',
	'inbox:delete:inbox:confirm' => 'Êtes-vous sûr de vouloir supprimes tous les messages sélectionnés ?',
	'inbox:delete:thread:confirm' => 'Êtes-vous sûr de vouloir supprimes tous les messages de ce fil ?',
	'inbox:delete:message:confirm' => 'Êtes-vous sûr de vouloir supprimes ce message ?',
	'inbox:markread:success' => '%s messages ont été marqués comme lus.',
	'inbox:markread:success:single' => 'Le message a été marqué comme lu.',
	'inbox:markread:error' => 'Les messages n\'ont pu être marqués comme lus.',
	'inbox:markunread:success' => '%s messages ont été marqués comme non-lus.',
	'inbox:markunread:success:single' => 'Le message a été marqué comme non-lu.',
	'inbox:markunread:error' => 'Les messages n\'ont pu être marqués comme non-lus.',
	'inbox:error:notfound' => '%s messages n\'ont pu être trouvés',
	'inbox:error:persistent' => '%s messages n\'ont pu être supprimés, ils sont en lecture seule.',
	'inbox:error:unknown' => '%s messages n\'ont pu être supprimés en raison d\'une erreur inconnue.',
	'inbox:send:success' => 'Le message a été envoyé.',
	'inbox:send:error:no_recipients' => 'Vous devez sélectionner un destinataire.',
	'inbox:send:error:no_body' => 'Vous devez ajouter un corps de texte au message.',
	'inbox:send:error:generic' => 'Une erreur inconnue est survenue lors de l\'envoi du message.',
	'inbox:user:unknown' => 'Inconnu',
	'inbox:form:toggle_all' => 'Basculer',
	'inbox:message:recipient' => 'Destinataire',
	'inbox:message:recipients' => 'Destinataires',
	'inbox:message:subject' => 'Sujet',
	'inbox:message:body' => 'Message',
	'inbox:message:attachments' => 'Pièces jointes',
	'inbox:message:attachments:add' => 'Ajouter une pièce jointe',
	'inbox:message:send' => 'Envoyer',
	'inbox:notification:subject' => 'Vous avez un nouveau %s',
	'inbox:notification:body' => "Vous avez reçu un nouveau %s de %s.


%s


Pour consulter vos messages, cliquez ici :

%s

Pour rédiger un message à %s, cliquez ici :

%s

Merci de ne pas répondre directement à ce message. Aucune suite ne sera donnée.",
);

