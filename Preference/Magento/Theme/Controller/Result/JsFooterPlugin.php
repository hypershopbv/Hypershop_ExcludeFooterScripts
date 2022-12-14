<?php
declare(strict_types=1);

namespace Hypershop\ExcludeFooterScripts\Preference\Magento\Theme\Controller\Result;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Store\Model\ScopeInterface;
use Magento\Theme\Controller\Result\JsFooterPlugin as MagentoJsFooterPlugin;

class JsFooterPlugin extends MagentoJsFooterPlugin
{
    private const XML_PATH_DEV_MOVE_JS_TO_BOTTOM = 'dev/js/move_script_to_bottom';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($scopeConfig);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Moves all Javascript tags to the end of body if this feature is enabled.
     *
     * @param Layout $subject
     * @param Layout $result
     * @param HttpResponseInterface|ResponseInterface $httpResponse
     * @return Layout (That should be void, actually)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRenderResult(Layout $subject, Layout $result, ResponseInterface $httpResponse)
    {
        if (!$this->isDeferEnabled()) {
            return $result;
        }

        $content = (string)$httpResponse->getContent();
        $bodyEndTag = '</body';
        $bodyEndTagFound = strrpos($content, $bodyEndTag) !== false;

        if ($bodyEndTagFound) {
            $scripts = $this->extractScriptTags($content);
            if ($scripts) {
                $newBodyEndTagPosition = strrpos($content, $bodyEndTag);
                $content = substr_replace($content, $scripts . "\n", $newBodyEndTagPosition, 0);
                $httpResponse->setContent($content);
            }
        }

        return $result;
    }

    /**
     * Extracts and returns script tags found in given content.
     *
     * @param string $content
     * @return string
     */
    private function extractScriptTags(&$content): string
    {
        $scripts = '';
        $scriptOpen = '<script';
        $scriptClose = '</script>';
        $scriptOpenPos = strpos($content, $scriptOpen);

        while ($scriptOpenPos !== false) {
            $scriptClosePos = strpos($content, $scriptClose, $scriptOpenPos);
            $script = substr($content, $scriptOpenPos, $scriptClosePos - $scriptOpenPos + strlen($scriptClose));
            $isXMagentoTemplate = strpos($script, 'text/x-magento-template') !== false;

            if ($isXMagentoTemplate) {
                $scriptOpenPos = strpos($content, $scriptOpen, $scriptClosePos);
                continue;
            }

            // HS: skip the scripts that contain excluded.
            if (strpos($script, 'excluded') !== false) {
                $scriptOpenPos = strpos($content, $scriptOpen, $scriptClosePos);
                continue;
            }

            $scripts .= "\n" . $script;
            $content = str_replace($script, '', $content);
            // Script cut out, continue search from its position.
            $scriptOpenPos = strpos($content, $scriptOpen, $scriptOpenPos);
        }

        return $scripts;
    }

    /**
     * Returns information whether moving JS to footer is enabled
     *
     * @return bool
     */
    private function isDeferEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DEV_MOVE_JS_TO_BOTTOM,
            ScopeInterface::SCOPE_STORE
        );
    }
}
