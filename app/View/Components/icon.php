<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Log;

class icon extends Component
{
    public $icon;
    public $width;
    public $height;
    public $viewBox;
    public $fill;
    public $strokeWidth;
    public $id;
    public $class;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $icon = null,
        $width = 24,
        $height = 24,
        $viewBox = '24 24',
        $fill = 'currentColor', // currentColor, none
        $strokeWidth = 2,
        $id = null,
        $class = null
    )
    {
        $this->icon = $icon;
        $this->width = $width;
        $this->height = $height;
        $this->viewBox = $viewBox;
        $this->fill = $fill;
        $this->strokeWidth = $strokeWidth;
        $this->id = $id ?? '';
        $this->class = $class ?? '';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.icon');
    }
}
