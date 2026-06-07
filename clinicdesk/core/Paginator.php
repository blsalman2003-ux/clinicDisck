<?php

class Paginator
{
    private int $totalItems;
    private int $perPage;
    private int $currentPage;

    public function __construct(int $totalItems, int $perPage, int $currentPage)
    {
        $this->totalItems  = max(0, $totalItems);
        $this->perPage     = max(1, $perPage);
        $this->currentPage = max(1, $currentPage);

        if ($this->currentPage > $this->totalPages() && $this->totalPages() > 0) {
            $this->currentPage = $this->totalPages();
        }
    }

    public function offset(): int
    {
        return ($this->currentPage - 1) * $this->perPage;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function totalPages(): int
    {
        if ($this->totalItems === 0) return 1;
        return (int) ceil($this->totalItems / $this->perPage);
    }

    public function currentPage(): int
    {
        return $this->currentPage;
    }

    public function hasPrev(): bool
    {
        return $this->currentPage > 1;
    }

    public function hasNext(): bool
    {
        return $this->currentPage < $this->totalPages();
    }

    public function totalItems(): int
    {
        return $this->totalItems;
    }
}
