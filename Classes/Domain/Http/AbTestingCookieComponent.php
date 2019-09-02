<?php

namespace Wysiwyg\ABTesting\Domain\Http;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Http\Component\ComponentContext;
use Neos\Flow\Http\Component\ComponentInterface;
use Neos\Flow\Http\Cookie;
use Wysiwyg\ABTesting\Domain\Model\Feature;
use Wysiwyg\ABTesting\Domain\Service\DecisionService;
use Wysiwyg\ABTesting\Domain\Service\FeatureService;

class AbTestingCookieComponent implements ComponentInterface
{
    const AB_TESTING_COOKIE_NAME = 'WYSIWYG_AB_TESTING'; //@todo Konfigurierbar

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
     * @param ComponentContext $componentContext
     * @return void
     * @api
     */
    public function handle(ComponentContext $componentContext)
    {
        $request = $componentContext->getHttpRequest();
        $response = $componentContext->getHttpResponse();

        if (!$request->hasCookie(self::AB_TESTING_COOKIE_NAME)) {
            // @todo lifetime konfigurierbar
            $abTestingCookie = new Cookie(self::AB_TESTING_COOKIE_NAME, null, strtotime('+2 years'), null, null, '/', false, false);

            $decisionsAsJson = json_encode($this->decisionService->decideForAllFeatures());
            $abTestingCookie->setValue($decisionsAsJson);

            $response->setCookie($abTestingCookie);
            $componentContext->replaceHttpResponse($response);

            return;
        }

        $abTestingCookie = $request->getCookie(self::AB_TESTING_COOKIE_NAME);
        $currentCookieValue = json_decode($abTestingCookie->getValue(), true);
        $activeFeatures = $this->featureService->getAllActiveFeatures();

        if (is_array($currentCookieValue)) {
            /**
             * @var Feature $activeFeature
             */
            foreach ($activeFeatures as $activeFeature) {
                $featureName = str_replace(' ', '_', $activeFeature->getFeatureName());
                if (!array_key_exists($featureName, $currentCookieValue)) {
                    $currentCookieValue[$featureName] = $this->decisionService->getDecisionForFeature($activeFeature);
                }
            }
        }

        $abTestingCookie->setValue(json_encode($currentCookieValue));

        $response->setCookie($abTestingCookie);
        $componentContext->replaceHttpResponse($response);
    }
}
