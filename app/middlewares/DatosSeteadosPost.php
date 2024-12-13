<?php
use Slim\Psr7\Response as ResponseClass;

class DatosSeteadosPostMiddleware
{
    private $validar;

    public function __construct($validar) 
    {
        $this->validar = $validar;
    }

    public function __invoke($request, $handler)
    {
        $params = $request->getParsedBody();

        foreach ($this->validar as $key => $value) 
        {
            if(!isset($params[$value]))
            {

                $response = new ResponseClass();

                $response->getBody()->write('{Error: "datos no seteados, falta '. $value . '"}');
                return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
            }
        }

        $response = $handler->handle($request);

        return $response; 
    }
}