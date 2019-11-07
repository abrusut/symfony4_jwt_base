<?php


namespace App\Serializer;


use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Esta clase permite agregar propiedades a la respuesta del objeto usuario, validando si el rol es admin
 * le agrega un "groups" a la configuracion (context) para que cuando serialize lo haga tambien sobre ese grupo get-admin
 * Class UserContextBuilder
 * @package App\Serializer
 */
class UserContextBuilder implements SerializerContextBuilderInterface
{

    /**
     * @var SerializerContextBuilderInterface
     */
    private $decorated;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Creates a serialization context from a Request.
     *
     * @throws RuntimeException
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context =$this->decorated->createFromRequest(
            $request,$normalization,$extractedAttributes
        );

        // Clase que sera serializada o deserializada
        $resourceClass = $context['resource_class'] ?? null;// default null

        if(User::class === $resourceClass &&
            isset($context['groups']) &&
            $normalization === true &&
            $this->authorizationChecker->isGranted(User::ROLE_ADMIN)
        )
        {
            $context['groups'][] = 'get-admin';
        }
        return $context;
    }
}
