<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Exception\EmptyBodyException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Esta clase se encarga de capturar el evento para un POST o PUT despues de el deserializer y validar que el body tenga contenido (json)
 * de lo contrario lanza una excepcion con un mensaje customizado
 * Class EmptyBodySubscriber
 * @package App\EventSubscriber
 */
class EmptyBodySubscriber implements EventSubscriberInterface
{
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['handleEmptyBody', EventPriorities::POST_DESERIALIZE]
        ];
    }
    
    public function handleEmptyBody(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();
        $route = $request->get('_route');
        
        // Se valida ademas del metodo, que el content type sea json ya que el admin panel envia form, de modo
        // extra se valida que la ruta comience con 'api' (API Platform)
        if(!in_array($method, [Request::METHOD_POST, Request::METHOD_PUT ]) ||
            in_array($request->getContentType(), ['html', 'form']) ||
            substr($route, 0,3) !== 'api'){
            return;
        }
        
        $data = $event->getRequest()->get('data');
        $files = $event->getRequest()->files;
        
        if(null === $data &&  null === $files){
            throw new EmptyBodyException();
        }
        
    }
}