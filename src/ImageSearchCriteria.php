<?php

namespace App;

use App\Exceptions\InvalidLimitException;
use App\Exceptions\InvalidPageException;

class ImageSearchCriteria
{
    private $tag;
    private $page;
    private $limit;

    /**
     * ImageSearchCriteria constructor.
     * @param $tag
     * @param $page
     * @param $limit
     * @throws InvalidLimitException
     * @throws InvalidPageException
     */
    public function __construct($tag, $page, $limit)
    {
        $this->tag = $tag;
        if ($page <= 0) {
            throw new InvalidPageException("Page must be positive");
        } else {
            $this->page = $page;
        }
        if ($limit <= 0) {
            throw new InvalidLimitException("Limit must be positive");
        } else {
            $this->limit = $limit;
        }
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $tag
     */
    public function setTag($tag): void
    {
        $this->tag = $tag;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param mixed $page
     */
    public function setPage($page): void
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

}