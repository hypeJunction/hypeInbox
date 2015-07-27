<?php

namespace hypeJunction\Inbox\Actions;

use ElggEntity;
use ElggFile;
use hypeJunction\Controllers\Action;
use hypeJunction\Exceptions\ActionValidationException;
use hypeJunction\Filestore\UploadHandler;
use hypeJunction\Inbox\AccessCollection;
use hypeJunction\Inbox\Group;
use hypeJunction\Inbox\Message;

/**
 * @property int           $guid
 * @property Message       $entity
 * @property int           $sender_guid
 * @property int[]         $recipient_guids
 * @property int[]         $attachment_guids
 * @property ElggEntity[]  $attachments
 * @property string        $subject
 * @property string        $body
 * @property string        $message_type
 */
class SendMessage extends Action {

	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		parent::setup();
		$this->entity = get_entity($this->guid);
		$this->sender_guid = $this->sender_guid ?: elgg_get_logged_in_user_guid();
		$this->recipient_guids = Group::create($this->recipient_guids)->guids();

		$this->attachment_guids = Group::create($this->attachments)->guids();
		unset($this->attachments);

		$this->subject = strip_tags((string) $this->subject);
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate() {

		if (empty($this->recipient_guids)) {
			throw new ActionValidationException(elgg_echo('inbox:send:error:no_recipients'));
		}

		if (empty($this->body)) {
			throw new ActionValidationException(elgg_echo('inbox:send:error:no_body'));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {

		if ($this->entity instanceof Message) {
			$this->message_hash = $this->entity->getHash();
			$this->message_type = $this->entity->getMessageType();
		} else {
			if (!$this->message_type) {
				$this->message_type = Message::TYPE_PRIVATE;
			}
		}

		// files being uploaded via $_FILES
		$uploads = UploadHandler::handle('attachments');
		if ($uploads) {
			foreach ($uploads as $upload) {
				if ($upload instanceof ElggFile) {
					$this->attachment_guids[] = $upload->guid;
				}
			}
		}

		$this->attachments = Group::create($this->attachment_guids)->entities();

		$access_id = AccessCollection::create(array($this->sender_guid, $this->recipient_guids))->getCollectionId();
		foreach ($this->attachments as $attachment) {
			$attachment->origin = 'messages';
			$attachment->access_id = $access_id;
			$attachment->save();
		}

		$guid = Message::factory(array(
					'sender' => $this->sender_guid,
					'recipients' => $this->recipient_guids,
					'subject' => $this->subject,
					'body' => $this->body,
					'message_hash' => $this->message_hash,
					'attachments' => $this->attachments,
				))->send();

		$this->entity = ($guid) ? get_entity($guid) : false;

		if (!$this->entity) {
			// delete attachment if message failed to send
			foreach ($this->attachments as $attachment) {
				$attachment->delete();
			}

			$this->result->addError(elgg_echo('inbox:send:error:generic'));
			return;
		}


		$sender = $this->entity->getSender();
		$this->message_type = $this->entity->getMessageType();
		$this->message_hash = $this->entity->getHash();

		$ruleset = hypeInbox()->config->getRuleset($this->message_type);

		$this->attachments = array_map(array(hypeInbox()->model, 'getLinkTag'), $this->attachments);

		$body = array_filter(array(
			($ruleset->hasSubject()) ? $this->entity->subject : '',
			$this->entity->getBody(),
			implode(', ', array_filter($this->attachments))
		));

		$notification_body = implode(PHP_EOL, $body);

		foreach ($this->recipient_guids as $recipient_guid) {
			$recipient = get_entity($recipient_guid);
			if (!$recipient) {
				continue;
			}

			$type_label = strtolower($ruleset->getSingularLabel($recipient->language));

			$subject = elgg_echo('inbox:notification:subject', array($type_label), $recipient->language);
			$notification = elgg_echo('inbox:notification:body', array(
				$type_label,
				$sender->name,
				$notification_body,
				elgg_view('output/url', array(
					'href' => $this->entity->getURL(),
				)),
				$sender->name,
				elgg_view('output/url', array(
					'href' => elgg_normalize_url("messages/thread/$this->message_hash#reply")
				)),
					), $recipient->language);

			$summary = elgg_echo('inbox:notification:summary', array($type_label), $recipient->language);

			notify_user($recipient->guid, $sender->guid, $subject, $notification, array(
				'action' => 'send',
				'object' => $this->entity,
				'summary' => $summary,
			));
		}

		$this->result->addMessage(elgg_echo('inbox:send:success'));
		$this->result->setForwardURL($this->entity->getURL());
	}

}
