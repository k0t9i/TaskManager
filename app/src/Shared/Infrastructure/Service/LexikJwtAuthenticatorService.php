<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\Exception\AuthenticationException;
use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\Repository\SharedUserRepositoryInterface;
use App\Shared\Domain\ValueObject\AuthUser;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Service\ValueObject\SymfonySecurityUser;
use ErrorException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LexikJwtAuthenticatorService implements AuthenticatorServiceInterface, EventSubscriberInterface
{
    private AuthUser $authUser;
    private string $pathRegexp = '';

    public function __construct(
        private readonly JWTTokenManagerInterface $tokenManager,
        private readonly TokenExtractorInterface $tokenExtractor,
        private readonly SharedUserRepositoryInterface $userRepository,
        private $path
    ) {
        $this->pathRegexp = '/' . str_replace('/', '\/', $this->path) . '/';
        try {
            preg_match($this->pathRegexp,'');
        } catch (ErrorException $e) {
            throw new LogicException(sprintf('Invalid path regexp "%s"', $this->path), 0, $e);
        }
        $this->authUser = new AuthUser('');
    }

    public function getAuthUser(): AuthUser
    {
        return $this->authUser;
    }

    public function getToken(string $id): string
    {
        return $this->tokenManager->create(new SymfonySecurityUser($id));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $uri = $event->getRequest()->getRequestUri();

        try {
            $token = $this->tokenExtractor->extract($event->getRequest());
            $token = $token === false ? '' : $token;

            try {
                if (!$payload = $this->tokenManager->parse($token)) {
                    throw new AuthenticationException('Invalid JWT Token');
                }
            } catch (JWTDecodeFailureException $e) {
                if (JWTDecodeFailureException::EXPIRED_TOKEN === $e->getReason()) {
                    throw new AuthenticationException('Expired token');
                }

                throw new AuthenticationException('Invalid JWT Token');
            }

            $idClaim = $this->tokenManager->getUserIdClaim();
            if (!isset($payload[$idClaim])) {
                throw new AuthenticationException(sprintf('Invalid payload "%s"', $idClaim));
            }

            $id = $payload[$idClaim];
            $user = $this->userRepository->findById(new UserId($id));
            if ($user === null) {
                throw new AuthenticationException(sprintf('User "%s" doesn\'t exist', $id));
            }

            $this->authUser = new AuthUser($id);
        } catch (AuthenticationException $e) {
            if (preg_match($this->pathRegexp, $uri) > 0) {
                throw $e;
            }
        }
    }
}
