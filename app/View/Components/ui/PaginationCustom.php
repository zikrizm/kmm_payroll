<?php

namespace App\View\Components\ui;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Log;

class PaginationCustom extends Component
{

    public $pagination;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.pagination-custom');
    }
}
