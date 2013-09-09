<?php

$english = array(

	'admin:inbox' => 'Inbox Settings',
	'admin:inbox:message_types' => 'Message Types',

	'item:object:message:create' => 'Add new message type',
	'item:object:message:name' => 'Unique type name',
	
	'item:object:message:setting:multiple' => 'Allow multiple recipients',
	'item:object:message:setting:attachments' => 'Allow file attachments',
	'item:object:message:setting:persistent' => 'Make persistent (can not be deleted by recipient)',
	'item:object:message:setting:policy' => 'Communication policy',
	'item:object:message:setting:policy:help' => 'Specifies sets of users between which this communication can occur.
		"Sender" and "Recipient" fields specify types of users (based on their "roles" on the site).
		"Relationship to recipient" specifies the type of relationship that must exist between the Sender and the Recipient for this communication to be permissible (e.g. Sender must be a friend of the Recipient).
		"Inverse relationship" specifies that the type of relationship should be inversed (e.g. Recipient must be a friend of the Sender for the Sender to be able to contact the Recipient)
		"Relationship of the sender to the group" creates an additional level of filtering, whereas 1) the Recipient must be a member of a group and 2) the Sender must have a specified relationship with that group (e.g. setting this to "member" would indicate that this type of communication can only occur between members of the same group)
	',

	'item:object:message:all' => 'All Messages',

	'hj:inbox:admin:import' => 'Import older messages',
	'hj:inbox:admin:import_stats' => '%s messages are lacking metadata information required for hypeInbox compatibility',
	'hj:inbox:admin:import_start' => 'Start Import',
	'hj:inbox:admin:import_complete' => 'Import complete',
	
	'hj:inbox:user_type:all' => 'Any user',
	'hj:inbox:user_type:admin' => 'Administrator',
	'hj:inbox:user_type:editor' => 'Editor',
	'hj:inbox:user_type:supervisor' => 'Supervisor',
	'hj:inbox:user_type:observer' => 'Observer',

	'item:object:message:label:singular' => 'Label (singular)',
	'item:object:message:label:plural' => 'Label (plural)',

	'hj:inbox:send' => 'Send %s',
	'hj:inbox:sender' => 'Sender',
	'hj:inbox:relationship' => 'Relationship to recipient',
	'hj:inbox:recipient' => 'Recipient',
	'hj:inbox:inverse_relationship' => 'Inverse relationship',
	'hj:inbox:relationship' => 'Relationship to recipient',
	'hj:inbox:group_relationship' => 'Relationship of the sender to the group that the recipient is a member of',

	'hj:inbox' => 'Messages',
	'hj:inbox:all' => 'All messages',
	'hj:inbox:inbox' => 'Inbox',
	'hj:inbox:sent' => 'Sent messages',
	'hj:inbox:compose' => 'Compose',

	'hj:inbox:usersettings:grouping' => 'Group inbox messages by sender',
	'hj:inbox:group' => 'Group',
	'hj:inbox:dontgroup' => 'Don\'t Group',

	'hj:inbox:message_not_found' => 'Message not found',
	
	'hj:inbox:untitled' => 'Untitled',
	'hj:inbox:you' => 'You',
	'hj:inbox:thread' => 'View all %s messages in this thread',
	'hj:inbox:thread:unread' => '%s new',

	'hj:inbox:message' => 'Message: %s',
	'hj:inbox:conversation' => 'Conversation between you and %s',

	'hj:inbox:nomessages' => 'There are no messages in this folder',

	'messages:attachments' => 'Attachments',

	'hj:inbox:load:before' => 'Load previous %s messages',
	'hj:inbox:load:after' => 'Load next %s messages',

	'hj:inbox:delete' => 'Delete',
	'hj:inbox:markread' => 'Mark as read',
	'hj:inbox:markunread' => 'Mark as unread',
	
	'hj:inbox:delete:success' => '%s of %s messages were successfully deleted',
	'hj:inbox:markread:success' => '%s of %s messages were marked as read',
	'hj:inbox:markunread:success' => '%s of %s messages were marked as unread',
	'hj:inbox:error:notfound' => '%s messages could not be found',
	'hj:inbox:error:persistent' => '%s messages cound not be delete due to their read-only setting',
	'hj:inbox:error:unknown' => '%s messages could not be deleted because of an unknown error',
	'hj:inbox:delete:thread:confirm' => 'Are you sure you want to delete all messages in this thread?',
	'hj:inbox:delete:message:confirm' => 'Are you sure you want to delete this message?',
	
);


add_translation("en", $english);
?>
