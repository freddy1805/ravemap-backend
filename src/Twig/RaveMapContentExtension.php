<?php

namespace App\Twig;

use App\Service\Entity\LocaleManager;
use App\Service\Entity\StaticPageManager;
use Doctrine\Common\Collections\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RaveMapContentExtension extends AbstractExtension
{
    private StaticPageManager $staticPageManager;
    private LocaleManager $localeManager;

    /**
     * RaveMapContentExtension constructor.
     * @param StaticPageManager $staticPageManager
     * @param LocaleManager $localeManager
     */
    public function __construct(StaticPageManager $staticPageManager, LocaleManager $localeManager)
    {
        $this->staticPageManager = $staticPageManager;
        $this->localeManager = $localeManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('main_nav_pages', [$this, 'getMainNavPages']),
            new TwigFunction('footer_nav_pages', [$this, 'getFooterNavPages']),
        ];
    }

    public function getMainNavPages(string $localeId): array
    {
        $locale = $this->localeManager->getLocaleByIdentifier($localeId);

        return $locale ? $this->staticPageManager->getMainNavPagesByLocale($locale) : [];
    }

    public function getFooterNavPages(string $localeId): array
    {
        $locale = $this->localeManager->getLocaleByIdentifier($localeId);

        return $locale ? $this->staticPageManager->getFooterNavPagesByLocale($locale) : [];
    }
}
