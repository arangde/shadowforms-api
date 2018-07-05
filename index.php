<?php
require_once $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 'autoloader.php';
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$app = new Slim\App();
$authenticationMiddleware = new AuthenticationMiddleware();

$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'x-requested-with, Accept-Ranges, Content-Encoding, Content-Length, Content-Range, Content-Type, origin, authorization, accept, client-security-token, fineform-access-token');
});

//Default route

$app->get('/', DefaultInterface::shared()->time());

// Administrator routes
$app->post('/admin/signin', UserInterface::shared()->authenticate());
$app->post('/admin/change-password', UserInterface::shared()->changePassword())->add($authenticationMiddleware);

$app->get('/admin/documents[/{page_size:[0-9]+}[/{page_index:[0-9]+}]]', DocumentInterface::shared()->get())->add($authenticationMiddleware);
$app->get('/admin/document/{id:[0-9]+}', DocumentInterface::shared()->getById())->add($authenticationMiddleware);
$app->delete('/admin/document/{id:[0-9]+}', DocumentInterface::shared()->delete())->add($authenticationMiddleware);
$app->post('/admin/documents/delete', DocumentInterface::shared()->delete())->add($authenticationMiddleware);
$app->post('/admin/document', DocumentInterface::shared()->create())->add($authenticationMiddleware);
$app->put('/admin/document/{id:[0-9]+}', DocumentInterface::shared()->update())->add($authenticationMiddleware);
$app->patch('/admin/document/{id:[0-9]+}', DocumentInterface::shared()->modify())->add($authenticationMiddleware);

// User routes

$app->get('/documents[/{page_size:[0-9]+}[/{page_index:[0-9]+}]]', DocumentInterface::shared()->get());
$app->get('/document/{id}', DocumentInterface::shared()->getById());
$app->get('/template/{key}', DocumentInterface::shared()->getByKey());

$app->run();
?>
