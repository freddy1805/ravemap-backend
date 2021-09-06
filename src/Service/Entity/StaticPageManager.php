<?php

namespace App\Service\Entity;

use App\Entity\Locale;
use App\Entity\StaticPage;
use Doctrine\Common\Collections\Collection;

class StaticPageManager extends BaseManager {

    protected string $repoName = 'App:StaticPage';

    protected array $validation = [
        'locale',
        'title',
        'content'
    ];

    /**
     * @param Locale $locale
     * @param string $slug
     * @return StaticPage|null
     */
    public function getPageByLocaleAndSlug(Locale $locale, string $slug): ?StaticPage
    {
        return $this->repository->findOneBy(['locale' => $locale, 'slug' => $slug]);
    }

    /**
     * @param Locale $locale
     * @return StaticPage[]
     */
    public function getPagesByLocale(Locale $locale): array
    {
        return $this->repository->findBy(['locale' => $locale]);
    }

    /**
     * @param Locale $locale
     * @return StaticPage[]
     */
    public function getMainNavPagesByLocale(Locale $locale): array
    {
        return $this->repository->findBy(['locale' => $locale, 'inMainNav' => true], ['navPosition' => 'ASC']);
    }

    /**
     * @param Locale $locale
     * @return StaticPage[]
     */
    public function getFooterNavPagesByLocale(Locale $locale): array
    {
        return $this->repository->findBy(['locale' => $locale, 'inFooterNav' => true], ['navPosition' => 'ASC']);
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return StaticPage::class;
    }
}
