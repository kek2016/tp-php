<?php

namespace Middleware;

/**
 * Description of newPHPClass
 *
 * @author asantos07
 */
class AuthMiddleware {

    public function __invoke($request, $response, $next) {
        $body = $request->getParsedBody();
        $route = $request->getAttribute('route');
        $arguments = $route->getArguments();
        $name = $route->getName();
        session_start();
        if (PHP_SESSION_ACTIVE != session_status()) {
            return $response->withStatus(303)->write(var_dump(session_name()));
//            ->withHeader("Location", HOST . "/frontend/#/login");
        } else {
            if ($name == 'registerJob') {
                $empresa = \Persistence\Persist::readObject($body['empresaId'], \Entity\Empresa::getExt());
                if ($empresa && $empresa->getId() == $_SESSION['userId'] && $_SESSION['userType'] == 'company') {
                    $next($request, $response);
                } else {
                    return $response->withStatus(401);
                }
            } elseif ($name == 'patch') {
                $entidade = \Persistence\Persist::readObject($body['empresaId'], '.' . $arguments['type']);
                if ($entidade && $entidade->getId() == $_SESSION['userId'] && $_SESSION['userType'] == $arguments['type']) {
                    $next($request, $response);
                } else {
                    return $response->withStatus(401);
                }
            }
        }
        return $response;
    }

}
