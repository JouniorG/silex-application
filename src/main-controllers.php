<?php
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use WaveLearning\Controllers\Page\PageControllers;
	use WaveLearning\Rest\Controllers\PanelEventControllers;
	use WaveLearning\Rest\Controllers\TokenControllers;

	Request::enableHttpMethodParameterOverride();
	header("access-control-allow-origin: *");

	$app->mount("/", new PageControllers());
	$app->mount("/restjson/modules", new PanelEventControllers());
	$app->mount("/restjson/token", new TokenControllers());