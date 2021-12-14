<?php

namespace Wysiwyg\ABTesting\Domain\Http\Middleware;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Cookie;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Service\DecisionService;
use Wysiwyg\ABTesting\Domain\Service\FeatureService;

class AbTestingCookieMiddleware implements MiddlewareInterface
{
    /**
     * @Flow\InjectConfiguration(package="Wysiwyg.ABTesting", path="cookie")
     * @var array
     */
    protected $cookieSettings;

    /**
     * @Flow\Inject
     * @var DecisionService
     */
    protected $decisionService;

    /**
     * @Flow\Inject
     * @var FeatureService
     */
    protected $featureService;

    /**
     * Sets an A/B Testing Cookie.
     * When no cookie is set, a new one with new decisions will be added.
     * When a cookie exists, the cookie value will be updated if features couldn't be found in the cookie.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @api
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $cookieParams = $request->getCookieParams();

        if (!array_key_exists($this->cookieSettings['name'], $cookieParams)) {
            return $this->createCookieToResponse($response);
        }

        return $this->refreshResponseCookie($request, $response);
    }

    /**
     * Creates a new A/B Testing Cookie with decisions of all features.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function createCookieToResponse(ResponseInterface $response): ResponseInterface
    {
        $abTestingCookie = new Cookie($this->cookieSettings['name'], null, strtotime($this->cookieSettings['lifetime']), null, null, '/', false, false);
        $decisionsAsJson = json_encode($this->decisionService->decideForAllFeatures());
        $abTestingCookie->setValue($decisionsAsJson);

        return $response->withAddedHeader('Set-Cookie', (string)$abTestingCookie);
    }

    /**
     * Refreshes the A/B Testing Cookie, if necessary.
     * Checks for current decisions and add new decisions for features without a decision to the cookie.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    protected function refreshResponseCookie(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $cookieParams = $request->getCookieParams();
        $abTestingCookie = $cookieParams[$this->cookieSettings['name']] ?? '';
        $currentCookieValue = json_decode(urldecode(urldecode($abTestingCookie)), true);
        $activeFeatures = $this->featureService->getAllActiveFeatures();

        if (is_array($currentCookieValue)) {
            /** @var Feature $activeFeature */
            foreach ($activeFeatures as $activeFeature) {
                $featureName = str_replace(' ', '_', $activeFeature->getFeatureName());

                if (!array_key_exists($featureName, $currentCookieValue)) {
                    $currentCookieValue[$featureName] = $this->decisionService->getDecisionForFeature($activeFeature);
                }
            }
        }

        $abTestingCookie = new Cookie($this->cookieSettings['name'], null, strtotime($this->cookieSettings['lifetime']), null, null, '/', false, false);
        $decisionsAsJson = json_encode($currentCookieValue);
        $abTestingCookie->setValue($decisionsAsJson);

        return $response->withAddedHeader('Set-Cookie', (string)$abTestingCookie);
    }
}
