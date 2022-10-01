<?php

namespace App\View\Components\ui;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Log;

class SortTable extends Component
{

    public $text;
    public $url;
    public $field;
    public $order;
    public $icon;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($text, $url, $field, $order = '', $icon = null)
    {
        $this->text = $text;
        $this->url = $url;
        $this->field = $field;
        $this->order = $order;
        $this->icon = $icon ?? 'chevron-down';
    }



    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.sort-table');
    }
}
