<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
if ($response->getStatusCode() == 500) {
    if (isset($response->exception) && $response->exception) {
        echo "Exception: " . get_class($response->exception) . "\n";
        echo "Message: " . $response->exception->getMessage() . "\n";
        echo "File: " . $response->exception->getFile() . ":" . $response->exception->getLine() . "\n";
        echo $response->exception->getTraceAsString();
    } else {
        echo "No exception payload attached to response.\n";
        echo substr($response->getContent(), 0, 1000); // Print a chunk of the response to see if there's a rendered error
    }
}
