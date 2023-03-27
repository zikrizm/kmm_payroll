<?php

namespace App\View\Components\ui;

use Illuminate\View\Component;

class SearchData extends Component
{
    public $placeholder;
    public $url;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($placeholder, $url = null)
    {
        $this->placeholder = $placeholder;
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.search-data');
    }
}
