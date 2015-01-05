hypeInbox
===========

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
