hypeInbox
===========
![Elgg 2.2](https://img.shields.io/badge/Elgg-2.2-orange.svg?style=flat-square)

Enhanced messaging for Elgg

## Features

* Define new message types and messaging rules (based on sender and recipient roles and relationships)
* Multiple inboxes for private messages and custom message types
* Message threading - messages are grouped based on message title and recipients
* Multi-user threads
* Real-time updates
* Message attachments (requires hypeAttachments)
* Integration with hypeApprove and ArckInteractive's roles (extendable via hooks)
* Message search

## Screenshots

![Compose a message interface](https://raw.github.com/hypeJunction/hypeInbox/master/screenshots/compose.png "Compose")
![Inbox view](https://raw.github.com/hypeJunction/hypeInbox/master/screenshots/inbox.png "Inbox")
![Popup view](https://raw.github.com/hypeJunction/hypeInbox/master/screenshots/popup.png "Popup")

## Acknowledgements

* Plugin is inspired and partially sponsored by Whitemoor School

## Usage / Configuration

The plugin allows you to define new types of communication between users. To configure new message types,
you can use recipient roles, sender roles and relationships between them

For example, if you would like to allow users to send 'homework' to their supervisors, you would use the following configuration:

 * Sender type: Any user
 * Recipient type: Supervisor
 * Relationship: Supervisor
 * Inverse relationship: yes
 * Group relationship: --blank--

If you would like to allow Editors to send 'notices' to Administrators, you would use the following:

 * Sender type: Editor
 * Recipient type: Administrator
 * Relationship: --blank--
 * Inverse relationship: --blank--
 * Group relationship: --blank--

If you would like to allow Group exchange between members of the same group, you would use the following:

 * Sender: Any user
 * Recipient: Any user
 * Relationship: --blank--
 * Inverse relationship: --blank--
 * Group relationship: member
