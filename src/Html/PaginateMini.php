<?php
namespace Tuum\Pagination\Html;

/**
 * Class ToBootstrap3
 *
 * to create pagination html for Bootstrap 3.
 *
 * @package WScore\Pagination\Html
 */
class PaginateMini extends AbstractPaginate
{
    /**
     * @return array
     */
    public function toArray()
    {
        // list of pages, from $start till $last.
        $page_list = $this->fillPages();

        $pages = [];
        $pages[] = $this->constructPage('prev', '&laquo;');
        if (!$this->checkIfInPageList('first', $page_list)) {
            $page_list = array_slice($page_list, 2);
            $pages[] = $this->constructPage('first', 1);
            $pages[] = [];
        }
        $pages = array_merge($pages, $page_list);
        if (!$this->checkIfInPageList('last', $page_list)) {
            $pages = array_slice($pages, 0, -2);
            $pages[] = [];
            $pages[] = $this->constructPage('last', $this->calcPageNum('last'));
        }
        $pages[] = $this->constructPage('next', '&raquo;');

        return $pages;
    }
}
