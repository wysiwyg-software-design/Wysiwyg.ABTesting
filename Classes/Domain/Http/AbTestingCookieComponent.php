<?php

namespace Wysiwyg\ABTesting\Domain\Http;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Component\ComponentContext;
use Neos\Flow\Http\Component\ComponentInterface;
use Neos\Flow\Http\Cookie;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Service\DecisionService;
use Wysiwyg\ABTesting\Domain\Service\FeatureService;

class AbTestingCookieComponent implements ComponentInterface
{
    /**
     * @Flow\InjectConfiguration(path="cookie")
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
     * @param ComponentContext $componentContext
     * @return void
     * @api
     */
    public function handle(ComponentContext $componentContext)
    {
        $request = $componentContext->getHttpRequest();
        $response = $componentContext->getHttpResponse();

        $cookieParams = $request->getCookieParams();
        if ($cookieParams && !array_key_exists($this->cookieSettings['name'], $cookieParams)) {
            $this->createCookieToResponse($componentContext, $response);
            return;
        }

        $this->refreshResponseCookie($componentContext, $request, $response);
    }

    /**
     * Creates a new A/B Testing Cookie with decisions of all features.
     *
     * @param ComponentContext $componentContext
     * @param ResponseInterface $response
     */
    private function createCookieToResponse(ComponentContext $componentContext, ResponseInterface $response)
    {
        $abTestingCookie = new Cookie($this->cookieSettings['name'], null, strtotime($this->cookieSettings['lifetime']), null, null, '/', false, false);
        $decisionsAsJson = json_encode($this->decisionService->decideForAllFeatures());
        $abTestingCookie->setValue($decisionsAsJson);

        $responseWithCookie = $response->withAddedHeader('Set-Cookie', (string)$abTestingCookie);
        $componentContext->replaceHttpResponse($responseWithCookie);
    }

    /**
     * Refreshes the A/B Testing Cookie, if necessary.
     * Checks for current decisions and add new decisions for features without a decision to the cookie.
     *
     * @param ComponentContext $componentContext
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    private function refreshResponseCookie(ComponentContext $componentContext, ServerRequestInterface $request, ResponseInterface $response)
    {
        $cookieParams = $request->getCookieParams();
        $abTestingCookie = $cookieParams[$this->cookieSettings['name']] ?? '';
        $currentCookieValue = json_decode($abTestingCookie, true);
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
        $responseWithCookie = $response->withAddedHeader('Set-Cookie', (string)$abTestingCookie);
        $componentContext->replaceHttpResponse($responseWithCookie);
    }
}
