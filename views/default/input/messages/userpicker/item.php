<?php

$entity = elgg_extract('entity', $vars);
$name = elgg_extract('name', $vars);
$checked = elgg_extract('checked', $vars, false);
$multiple = elgg_extract('multiple', $vars, false);

if (!elgg_instanceof($entity)) {
	return;
}

$icon = elgg_view('output/img', array(
	'src' => $entity->getIconURL('tiny')
));

if ($multiple) {
	$checkbox = elgg_view('input/checkbox', array(
		'name' => "{$name}[]",
		'value' => $entity->guid,
		'checked' => $checked,
		'default' => false
	));
} else {
	$attr = elgg_format_attributes(array(
		'type' => 'radio',
		'name' => "{$name}[]",
		'value' => $entity->guid,
		'checked' => $checked,
	));
	$checkbox = "<input $attr />";
}
switch ($entity->getType()) {

	case 'user' :
	case 'group' :

		$link = elgg_view('output/url', array(
			'text' => $entity->name,
			'href' => $entity->getURL(),
		));

		break;

	case 'object' :
		$link = elgg_view('output/url', array(
			'text' => $entity->title,
			'href' => $entity->getURL(),
		));
		break;
}


$image_block = elgg_view_image_block($icon, $link, array(
	'image_alt' => $checkbox
		));
echo <<<__HTML
<div class="userpicker-image-block" data-guid="$entity->guid">
	$image_block
</div>
__HTML;
