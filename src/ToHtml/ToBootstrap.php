<?php
namespace Tuum\Pagination\ToHtml;

use Tuum\Pagination\Paginate\PaginateInterface;

class ToBootstrap implements ToHtmlInterface
{
    /**
     * @var array
     */
    public $labels = [
        'first'     => '&laquo;',
        'prev'      => '<',
        'next'      => '>',
        'last'      => '&raquo;',
    ];

    /**
     * @var string
     */
    public $ul_class = 'pagination';

    /**
     * @var string
     */
    public $default_type = 'disable';

    public $empty_li = "<li class=\"disable\"><a href=\"#\" >...</a></li>\n";
    
    /**
     * must be an output from PaginateInterface's toArray() method.
     *
     * @var array
     */
    private $pages;

    /**
     * @param array $labels
     */
    public function __construct(array $labels = [])
    {
        $this->labels = $labels + $this->labels;
    }

    public function withPaginate(PaginateInterface $paginate)
    {
        $self = clone($this);
        $self->pages = $paginate->toArray();
        return $self;
    }

    /**
     * @param array $labels
     * @return ToBootstrap
     */
    public static function forge(array $labels = [])
    {
        return new self($labels);
    }

    /**
     * @param array $labels
     * @return $this
     */
    public function setLabels(array $labels)
    {
        $this->labels = array_merge($this->labels, $labels);
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $html = '';
        foreach ($this->pages as $info) {
            $html .= $this->listItem($info);
        }

        return "<ul class=\"{$this->ul_class}\">\n{$html}</ul>\n";
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param array $info
     * @return string
     */
    private function listItem(array $info)
    {
        if (empty($info)) {
            return $this->empty_li;
        }
        $rel   = isset($info['rel']) ? $info['rel'] : '';
        $href  = isset($info['href']) ? $info['href'] : '';
        $aria  = isset($info['aria']) ? $info['aria'] : '';
        $label = isset($info['label']) && $info['label'] ? $info['label'] : 
            (isset($this->labels[$rel]) ? $this->labels[$rel] : $rel);
        return $this->bootLi($label, $href, $aria);
    }

    /**
     * @param string $label
     * @param string $href
     * @param string $aria
     * @return string
     */
    private function bootLi($label, $href, $aria)
    {
        $srLbl = $aria ? " aria-label=\"{$aria}\"" : '';
        if ($href != '#') {
            $html = "<li><a href=\"{$href}\"";
            $html .= $srLbl . " >{$label}</a></li>\n";
        } elseif (is_numeric($label)) {
            $html = "<li class=\"active\"><a href=\"#\" >{$label}</a></li>\n";
        } else {
            $html = "<li class=\"disabled\"><a href=\"#\" >{$label}</a></li>\n";
        }
        return $html;
    }
}