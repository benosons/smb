<?php

namespace Application\Master;

class RouteNotFoundController extends GlobalActionController
{

    /**
     * Not Found Route for api give an error to api
     * 
     * @return JSON
     */
    public function routenotfoundAction()
    {
        $config = $this->getEvent()->getParam('config', false);
        $this->httpStatusCode = 404;
        $this->apiResponse = [$config['ApiRequest']['responseFormat']['errorKey'] => $config['ApiRequest']['responseFormat']['pageNotFoundKey']];
        return $this->createResponse();
    }
}
