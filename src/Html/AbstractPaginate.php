<?php
namespace Tuum\Pagination\Html;

use Tuum\Pagination\Inputs;

abstract class AbstractPaginate implements PaginateInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var Inputs
     */
    protected $inputs;

    /**
     * @var int
     */
    protected $currPage;

    /**
     * @var array
     */
    public $aria_label = [
        'first' => 'first page',
        'prev'  => 'previous page',
        'next'  => 'next page',
        'last'  => 'last page',
    ];

    public $num_links = 5;

    /**
     * @param array $aria
     */
    public function __construct(array $aria=[])
    {
        $this->aria_label = $aria + $this->aria_label;
    }

    /**
     * @param array $aria
     * @return AbstractPaginate
     */
    public static function forge(array $aria=[])
    {
        $self = new static($aria);
        return $self;
    }

    /**
     * @param int $num
     * @return $this
     */
    public function numLinks($num)
    {
        $this->num_links = $num;
        return $this;
    }

    /**
     * @API
     * @param Inputs $inputs
     * @return $this
     */
    public function withInputs(Inputs $inputs)
    {
        $self           = clone($this);
        $self->path     = $inputs->path;
        $self->inputs   = $inputs;
        $self->currPage = $inputs->getPage();
        return $self;
    }

    /**
     * @return array
     */
    protected function fillPages()
    {
        $numLinks = $this->num_links;
        $currPage = $this->inputs->getPage();
        $lastPage = $this->inputs->calcLastPage();

        $extra_1  = max(0, $numLinks - $currPage);
        $extra_2  = max(0, $numLinks - ($lastPage - $currPage));
        if ($extra_1 > 0 || $currPage === $numLinks) {
            $extra_2 += $extra_1 + 1;
        }
        if ($extra_2 > 0) {
            $extra_1 += $extra_2;
        }
        $start    = max($currPage - $numLinks - $extra_1, $this->inputs->calcFirstPage());
        $last     = min($currPage + $numLinks + $extra_2, $this->inputs->calcLastPage());

        $pages = [];
        for ($page = $start; $page <= $last; $page++) {
            $pages[$page] = $this->constructPage($page);
        }
        return $pages;
    }

    /**
     * @param string $page
     * @param array  $pages
     * @param array  $page_list
     * @return array
     */
    protected function constructPageIfNotInPages($page, array $pages, array $page_list)
    {
        $pageNum = $this->calcPageNum($page);
        if (!isset($page_list[$pageNum])) {
            $pages[] = $this->constructPage($page);
        }
        return $pages;
    }

    /**
     * @param string|int $page
     * @return array
     */
    protected function constructPage($page)
    {
        $pageNum = $this->calcPageNum($page);
        $href = ($pageNum == $this->inputs->getPage()) ?
            '#' : $this->inputs->getPath($pageNum);
        return ['rel' => $page, 'href' => $href];
    }

    /**
     * @param array $pages
     * @return array
     */
    protected function addAriaLabel(array $pages)
    {
        foreach ($pages as $key => $page) {
            $rel                 = $page['rel'];
            $pages[$key]['aria'] = isset($this->aria_label[$rel]) ? $this->aria_label[$rel] : '';
        }
        return $pages;
    }

    /**
     * @param $page
     * @return int|string
     */
    protected function calcPageNum($page)
    {
        if (!is_numeric($page) && is_string($page)) {
            $method  = 'calc' . ucwords($page) . 'Page';
            $pageNum = $this->inputs->$method();
            return $pageNum;
        } else {
            $pageNum = $page;
            return $pageNum;
        }
    }

}