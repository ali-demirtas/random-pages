<?php

class RandomPages extends Plugin
{

    public function init()
    {
        // Fields and default values for the database of this plugin
        $this->dbFields = array(
            'label'=>'Random Pages',
            'amountOfItems'=>5
        );
    }

    /**
     * [getRandomPages]
     * @return [type] array [Random pages results]
     */
    protected function getRandomPages()
    {

        global $dbPages;

        // Page number the first one
        $pageNumber = 1;

        // get all pages
        $amountOfItems = -1;

        // Only published pages
        $onlyPublished = true;

        // Get the list of pages
        $pages = $dbPages->getList($pageNumber, $amountOfItems, $onlyPublished);

        $totalPages = count($pages);

        $results = [];

        $randomPagesCount = (intval($this->getValue('amountOfItems')) >= 1) ? intval($this->getValue('amountOfItems')) : 1;

        /**
         * Get random page keys only if
         * - total pages in db >= 1
         * - config <= total pages in db
         */
        if ($totalPages >= 1 && $randomPagesCount <= $totalPages) {
            $rand_pages = array_rand($pages, $randomPagesCount);

            /**
             * array_rand() returns datatype int if 1 , array datatype if >= 2
             */
            if (is_array($rand_pages)) {
                foreach ($rand_pages as $pageKey) {
                    $results[] = [
                        'page' => $pages[$pageKey],
                    ];
                }
            } else {
                $results[] = [
                    'page' => $pages[$rand_pages],
                ];
            }
        }

        return $results;
    }

    // Method called on the settings of the plugin on the admin area
    public function form()
    {
        global $Language;

        $html  = '<div>';
        $html .= '<label>'.$Language->get('Label').'</label>';
        $html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
        $html .= '<span class="tip">' . $Language->get('This title is almost always used in the sidebar of the site').'</span>';
        $html .= '</div>';

        $html .= '<div>';
        $html .= '<label>'.$Language->get('Amount of items').'</label>';
        $html .= '<input id="jsamountOfItems" name="amountOfItems" type="text" value="'.$this->getValue('amountOfItems').'">';
        $html .= '</div>';

        return $html;
    }

    // Method called on the sidebar of the website
    public function siteSidebar()
    {
        global $Language;
        global $dbPages;

        $results = $this->getRandomPages();

        /**
         * Display only if results are found
         */
        if (!empty($results)) {
            // HTML for sidebar
            $html  = '<div class="plugin plugin-pages">';
            $html .= '<h2 class="plugin-label">'.$this->getValue('label').'</h2>';
            $html .= '<div class="plugin-content">';
            $html .= '<ul>';

            // Display results
            foreach ($results as $result) {
                // Create the page object from the page key
                $page = buildPage($result['page']);
                $html .= '<li>';
                $html .= '<a href="'.$page->permalink().'">';
                $html .= $page->title();
                $html .= '</a>';
                $html .= '</li>';
            }
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
            return $html;
        }
    }
}
