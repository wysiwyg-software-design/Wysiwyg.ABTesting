<?php
namespace Wysiwyg\ABTesting\Domain\Http;

use Neos\Flow\Http\Component\ComponentContext;
use Neos\Flow\Http\Component\ComponentInterface;
use Neos\Flow\Http\Cookie;
use Neos\Flow\Http\Request;
use Neos\Flow\Http\Response;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Service\DecisionService;
use Wysiwyg\ABTesting\Domain\Service\FeatureService;
use Neos\Flow\Annotations as Flow;
use Wysiwyg\CookieHandling\Domain\Service\CookieConsentService;

class AbTestingCookieComponent implements ComponentInterface
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
     * @Flow\Inject
     * @var CookieConsentService
     */
    protected $cookieConsentService;

    /**
     * Sets an A/B Testing Cookie.
     * When no cookie is set, a new one with new decisions will be added.
     * When a cookie exists, the cookie value will be updated if features couldn't be found in the cookie.
     *
     * @param ComponentContext $componentContext
     *
     * @return void
     * @api
     */
    public function handle(ComponentContext $componentContext)
    {
        $request = $componentContext->getHttpRequest();
        $response = $componentContext->getHttpResponse();

        $cookieName = $this->cookieSettings['name'] ?? 'WYSIWYG_AB_TESTING';

        $abTestingCookie = new Cookie($cookieName, null, strtotime($this->cookieSettings['lifetime']), null, null, '/', false, false);

        if ($request->hasCookie($cookieName)) {
            $currentCookie = $request->getCookie($cookieName);
            $currentCookieValue = json_decode($currentCookie->getValue(), true);
            $activeFeatures = $this->featureService->getAllActiveFeatures();

            if(is_array($currentCookieValue)) {

                /**
                 * @var Feature $activeFeature
                 */
                foreach ($activeFeatures as $activeFeature) {
                    $featureName = str_replace(' ', '_', $activeFeature->getFeatureName());

                    if (!array_key_exists($featureName, $currentCookieValue)) {
                        $currentCookieValue[$featureName] = $this->decisionService->getDecisionForFeature($activeFeature);
                    }
                }

            } else {
                $currentCookieValue = $this->decisionService->decideForAllFeatures();
            }

            $abTestingCookie->setValue(json_encode($currentCookieValue));

        } else {
            $decisionsAsJson = json_encode($this->decisionService->decideForAllFeatures());
            $abTestingCookie->setValue($decisionsAsJson);
        }

        $this->cookieConsentService->tryAddCookie($abTestingCookie);
        $componentContext->replaceHttpResponse($response);
    }
}
