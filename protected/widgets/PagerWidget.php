<?php

class PagerWidget extends CWidget
{

    public $add_get_params = '';
    public $id_update = 'list_update';
    public $count = 0;
    public $limit = 10;

    public function run()
    {
        $countPage = 0;

        if ($this->limit) {
            $countPage = (int) ($this->count / $this->limit);
            if ($this->count % $this->limit != 0) {
                $countPage++;
            }
        }

        $html = '';

        $activePage = $this->getPage();

        if ($countPage > 1 && $activePage <= $countPage) {
            $html .= '<div class="pagination">';

            $startPage = ($activePage > 2) ? $activePage - 2 : 1;
            $endPage = $startPage + 4;
            if ($endPage - $countPage > 0) {
                $startPage = $startPage - ($endPage - $countPage);
            }

            if ($activePage > 1) {
                $html .= $this->createButton('', '', $activePage - 1, '&laquo;');
            }
            if ($activePage > 3) {
                $html .= $this->createButton('', '',  1, 1);
            }

            if ($startPage > 2) {
                $html .= '<a>...</a>';
            }

            for ($i = 1; $i <= $countPage; $i++) {
                if (($i >= $startPage && $i <= $endPage) || $activePage == $i) {
                    $li_class = '';
                    $a_class = '';
                    if ($i == $activePage)
                        $a_class .= ' active';

                    $html .= $this->createButton($li_class, $a_class, $i, $i);
                }
            }

            if ($activePage < $countPage - 3) {
                $html .= '<a>...</a>';
            }


            if ($activePage < $countPage - 2) {
                $html .= $this->createButton('', '', $countPage, $countPage);
            }

            if ($activePage < $countPage) {
                $html .= $this->createButton('', '', $activePage + 1, '&raquo;');
            }

            $html .= '</div>';
        }
        
        echo $html;
    }

    function createButton($li_class, $link_class, $page, $label, $is_url = true)
    {
        $url = Yii::app()->request->requestUri;
        $activePage = $this->getPage();
        $expl = explode('page-', $url);
        $url = $expl[0] . '/page-' . $page . '/';
        $url = str_replace('/page-1/', '/', $url);
        $url = str_replace('//', '/', $url);

        $html = '';
        if ($is_url) {
            $html = '<a class="' . $link_class . '" href="' . $url . '">' . $label . '</a>';
        } else {
            $html = '<a class="' . $link_class . '" href="' . $url . '">' . $label . '</a>';
        }

        return $html;
    }

    private function getPage()
    {
        $url = Yii::app()->request->requestUri;
        $expl = explode('page-', $url);
        if (isset($expl[1])) {
            return (int)end($expl);
        } else {
            return 1;
        }
    }

}
