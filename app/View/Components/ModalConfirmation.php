<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Log;

class ModalConfirmation extends Component
{
    public $title;
    public $subTitle;
    public $classSubmit;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $title = 'Confirmation',
        $subTitle = 'Are you sure you want to delete this data?',
        $classSubmit = null
    ) {
        $this->title = $title;
        $this->subTitle = $subTitle;
        $this->classSubmit = $classSubmit ?? '';
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal-confirmation');
    }
}
