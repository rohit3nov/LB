<?php

require __DIR__.'/../bootstrap.php';

use App\Actions\ApplicationHealthCheckAction;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\controllers\CityController;
use App\services\CsvParser;
/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();


$app->addBodyParsingMiddleware();

// Add Routing Middleware
$app->addRoutingMiddleware();

/**
 * Container
 */
$container = new League\Container\Container();


/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes
$app->put('/cities/{city_id}',
    function (Request $request, Response $response, $args) use ($container) {
        $cityData = $request->getParsedBody();
        $cityData['city_id'] = $args['city_id'];

        // storage/worldcities.csv parser
        $parser  = new CsvParser();

        $cc = new CityController($parser,$cityData);
        $cc->addCity();
        $response->getBody()->write('City added successfuly!');
        return $response;

    }
);

// Run app
$app->run();

