hypeInbox
===========

Enhanced inbox and user-to-user messaging for Elgg

## Features ##

* Define new message types and messaging rules (based on sender and recipient roles and relationships)
* Multiple inboxes for private messages, notifications and custom message types
* Message threading - messages are grouped based on message title and recipients
* Multi-user threads
* Message attachments
* Policing includes user roles integration with hypeApprove and roles (extendable via hooks)

## Attributions / Credits ##

* UI Icon by Glyphicons http://glyphicons.com/

## Usage / Configuration ##

The plugin allows you to define new types of communication between users. To configure new message types,
you can use recipient roles, sender roles and relationships between them.

For example, if you would like to allow users to send 'homework' to their supervisors,
you would use the following configuration:
Sender type: Any user
Recipient type: Supervisor
Relationship: Supervisor
Inverse relationship: yes

Another example, if you would like to allow Editors to send 'notices' to Administrators, you would use the following:
Sender type: Editor
Recipient type: Administrator
Relationship: --blank--
Inverse relationship: --blank--

## Notes ##

* hypeFramework is not required


