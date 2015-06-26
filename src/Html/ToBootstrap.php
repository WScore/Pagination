<?php
namespace WScore\Pagination\Html;

use WScore\Pagination\ToStringInterface;

class ToBootstrap extends AbstractBootstrap implements ToStringInterface
{
    /**
     * @var array
     */
    protected $options = [
        'top'       => '&laquo;',
        'prev'      => 'prev',
        'next'      => 'next',
        'last'      => '&raquo;',
        'num_links' => 5,
    ];

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->options = $options + $this->options;
    }

    /**
     * @param int $numLinks
     * @return array
     */
    protected function calculatePagination($numLinks = 5)
    {
        $lastPage = $this->findLastPage($numLinks);
        $currPage = $this->inputs->getCurrPage();

        $pages   = [];
        $pages[] = ['label' => 'top',  'page' => 1]; // top
        $pages[] = ['label' => 'prev', 'page' => max($currPage - 1, 1)]; // prev

        // list of pages, from $start till $last. 
        $pages   = array_merge($pages, $this->fillPages($numLinks));

        $pages[] = ['label' => 'next', 'page' => min($currPage + 1, $lastPage)]; // next
        $pages[] = ['label' => 'last', 'page' => $lastPage]; // last
        return $pages;
    }

    /**
     * @param array $info
     * @return string
     */
    protected function listItem(array $info)
    {
        $label = isset($info['label']) ? $info['label'] : '';
        $page  = isset($info['page']) ? $info['page'] : '';
        $type  = isset($info['type']) ? $info['type'] : '';
        return $this->bootLi($label, $page, $type);
    }

    /**
     * @param string $label
     * @param int    $page
     * @param string $type
     * @return string
     */
    protected function bootLi($label, $page, $type = 'disable')
    {
        $label = isset($this->options[$label]) ? $this->options[$label] : $label;
        if ($page != $this->currPage) {
            $key  = $this->inputs->pagerKey;
            $html = "<li><a href='{$this->getRequestUri()}{$key}={$page}' >{$label}</a></li>\n";
        } elseif ($type == 'disable') {
            $html = "<li class='disabled'><a href='#' >{$label}</a></li>\n";
        } else {
            $html = "<li class='active'><a href='#' >{$label}</a></li>\n";
        }
        return $html;
    }
}