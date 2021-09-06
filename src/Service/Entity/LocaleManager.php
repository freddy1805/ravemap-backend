<?php

namespace App\Service\Entity;

use App\Entity\Locale;

class LocaleManager extends BaseManager {

    protected string $repoName = 'App:Locale';

    protected array $validation = [
        'name',
        'localeId'
    ];

    /**
     * @param string $localeId
     * @return Locale|null
     */
    public function getLocaleByIdentifier(string $localeId): ?Locale
    {
        return $this->repository->findOneBy(['localeId' => $localeId]);
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return Locale::class;
    }
}
