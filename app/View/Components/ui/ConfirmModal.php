<?php

namespace App\View\Components\ui;

use Illuminate\View\Component;

class ConfirmModal extends Component
{
    public $title;
    public $subTitle;
    public $class;
    public $method;
    public $submitLabel;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $title = 'Confirmation',
        $subTitle = 'Are you sure you want to delete this data?',
        $class = null,
        $method = 'DELETE',
        $submitLabel = 'Delete',
    ) {
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->class = $class ?? '';
        $this->method = $method;
        $this->submitLabel = $submitLabel;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ui.confirm-modal');
    }
}
