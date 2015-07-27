<?php

namespace hypeJunction\Inbox\Actions;

use ElggPlugin;
use hypeJunction\Controllers\Action;

final class SavePluginSettings extends Action {

	private $plugin;
	
	/**
	 * {@inheritdoc}
	 */
	public function setup() {
		$this->plugin = elgg_get_plugin_from_id('hypeInbox');
		$this->params = (array) get_input('params', array());
		$this->params['message_types'] = $this->prepareMessageTypes();
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate() {
		
		if (!$this->plugin instanceof ElggPlugin) {
			$this->result->addError(elgg_echo('plugins:settings:save:fail', array('hypeInbox')));
			return false;
		}

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function execute() {

		$plugin_name = $this->plugin->getManifest()->getName();

		foreach ($this->params as $k => $v) {
			if (is_array($v)) {
				$v = serialize($v);
			}
			$result = $this->plugin->setSetting($k, $v);
			if (!$result) {
				$this->result->addError(elgg_echo('plugins:settings:save:fail', array($plugin_name)));
			}
		}

		if ($result) {
			$this->result->addMessage(elgg_echo('plugins:settings:save:ok', array($plugin_name)));
		}
	}

	protected function prepareMessageTypes() {

		$config = array();
		$message_types = get_input('message_types');
		foreach ($message_types as $name => $options) {

			if (empty($options['name'])) {
				continue;
			}

			if ($name == '__new') {
				$name = strtolower(str_replace(' ', '_', $options['name']));
			}

			$config[$name] = array(
				'labels' => $options['labels'],
				'attachments' => elgg_extract('attachments', $options, false),
				'persistent' => elgg_extract('persistent', $options, false),
				'multiple' => elgg_extract('multiple', $options, false),
				'no_subject' => elgg_extract('no_subject', $options, false),
			);

			if (isset($options['policy'])) {
				for ($i = 0; $i < count($options['policy']['sender']); $i++) {
					$config[$name]['policy'][$i] = array(
						'sender' => $options['policy']['sender'][$i],
						'recipient' => $options['policy']['recipient'][$i],
						'relationship' => $options['policy']['relationship'][$i],
						'inverse_relationship' => $options['policy']['inverse_relationship'][$i],
						'group_relationship' => $options['policy']['group_relationship'][$i],
					);
				}
			}
		}

		return $config;
	}

}
