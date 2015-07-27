hypeInbox
===========
![Elgg 1.11](https://img.shields.io/badge/Elgg-1.11.x-orange.svg?style=flat-square)
![Elgg 1.12](https://img.shields.io/badge/Elgg-1.12.x-orange.svg?style=flat-square)

Enhanced inbox and user-to-user messaging for Elgg

## Features ##

* Define new message types and messaging rules (based on sender and recipient roles and relationships)
* Multiple inboxes for private messages, notifications and custom message types
* Message threading - messages are grouped based on message title and recipients
* Multi-user threads
* Message attachments (requires elgg_dropzone https://github.com/hypeJunction/elgg_dropzone)
* Policing includes user roles integration with hypeApprove and roles (extendable via hooks)

## Screenshots ##

![alt text](https://raw.github.com/hypeJunction/hypeInbox/master/screenshots/compose.png "Compose")
![alt text](https://raw.github.com/hypeJunction/hypeInbox/master/screenshots/inbox.png "Inbox")

## Attributions / Credits ##

* Plugin is inspired and partially sponsored by Whitemoor School

## Usage / Configuration ##

The plugin allows you to define new types of communication between users. To configure new message types,
you can use recipient roles, sender roles and relationships between them

For example, if you would like to allow users to send 'homework' to their supervisors,
you would use the following configuration:
Sender type: Any user
Recipient type: Supervisor
Relationship: Supervisor
Inverse relationship: yes
Group relationship: --blank--

Another example, if you would like to allow Editors to send 'notices' to Administrators, you would use the following:
Sender type: Editor
Recipient type: Administrator
Relationship: --blank--
Inverse relationship: --blank--
Group relationship: --blank--

Another example, if you would like to allow Group exchange between members of the same group, you would use the following:
Sender: Any user
Recipient: Any user
Relationship: --blank--
Inverse relationship: --blank--
Group relationship: member

## Upgrades

### Upgrading to 3.1

Focus of 3.1 is improved performance. As such, config values are no longer populated on each page load,
if you were relying on ```elgg_get_config()``` for any of the following values:
* inbox_message_types
* inbox_user_types
* inbox_user_relationships
* inbox_user_group_relationships

replace them correspondingly with:
* ```hypeInbox()->config->getMessageTypes()```
* ```hypeInbox()->config->getUserTypes()```
* ```hypeInbox()->config->getUserRelationships();```
* ```hypeInbox()->config->getUserGroupRelationships()```