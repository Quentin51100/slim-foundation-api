<?php
declare(strict_types=1);

namespace FoundationApi\ErrorHandlers;

use FoundationApi\ErrorHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Throwable;

/**
 * Class gérant le retour des Exceptions par défaut
 */
class DefaultErrorHandler extends ErrorHandler
{

    /**
     * Handler
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Throwable $exception
     * @param bool $displayErrorDetails unused // false
     * @param bool $logErrors unused // true
     * @param bool $logErrorDetails unused // true
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): ResponseInterface
    {
        /** @var LoggerInterface $logger */
        $logger = $this->container->get(LoggerInterface::class);
        $response = (new ResponseFactory())->createResponse();
        $logger->warning(
            "Une erreur s'est produite :" . PHP_EOL . $exception::class .
            "(" . $exception->getCode() . ") " . $exception->getMessage(),
            [
                "file" => $exception->getFile(),
                "line" => $exception->getLine(),
                "trace" => $exception->getTrace()
            ]
        );
        $this->logException($logger, $exception);
        $this->logRequest($logger, $request);
        return $this->formatResponse($response, 500, $exception->getMessage());
    }
}
