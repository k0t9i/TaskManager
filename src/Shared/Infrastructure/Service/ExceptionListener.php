<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Exception\DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Throwable;

final class ExceptionListener
{
    public function __construct(private readonly DomainExceptionToHttpStatusCodeMapper $codeMapper)
    {
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $this->getParentDomainExceptionIfExists($event->getThrowable());

        $code = $this->codeMapper->getHttpStatusCode($exception);
        $event->setResponse(
            new JsonResponse(
                [
                    'code' => $code,
                    'message' => $exception->getMessage(),
                ],
                $code
            )
        );
    }

    private function getParentDomainExceptionIfExists(Throwable $exception): Throwable
    {
        $result = $exception;
        while ($result->getPrevious() !== null) {
            if ($result instanceof DomainException) {
                return $result;
            }
            $result = $result->getPrevious();
        }
        return $exception;
    }
}
