<?php
namespace Wysiwyg\ABTesting\Domain\Comparator;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
class UserIdentifierComparator implements ComparatorInterface
{
    /**
     * @var int
     */
    private $comparisonValue;

    public function getComparisonValue(): int
    {
        if ($this->comparisonValue !== null) {
            return $this->comparisonValue;
        }

        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'no agent';
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'no accept language';

        $identifierString = sprintf("%s°%s°%s", $userAgent, $acceptLanguage, $this->getClientIp());
        $hash = md5($identifierString);
        $sixLastHexDigits = substr($hash, -6);

        $this->comparisonValue = HexToIntDowncast::sixDigitHexToPercentageInteger($sixLastHexDigits);
        return $this->comparisonValue;
    }

    /**
     * @return string
     */
    private function getClientIp()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] as $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } elseif (isset($_SERVER['HTTP_X_REAL_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }

        return $ip;
    }
}
