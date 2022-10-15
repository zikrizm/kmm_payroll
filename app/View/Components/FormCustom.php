<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormCustom extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form');
    }


    public static function applyClass()
    {
        return "call from helper to";
    }


    /**
     * Get the view/fill in the input form
     *
     * @param string $name
     * @param string $value
     * @param array $attributes
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public static function input($name = '', $value = '', $attributes = [])
    {
        try {
            $atr = (object)[
                "id" => '',
                "type" => 'text',
                "required" => '',
                "readonly" => false,
                "disabled" => false,
                "block_input" => false,
                "class" => '',
                "errormsg" => '',
                "label" => '',
                "labelclass" => '',
                "hint" => '',
                "hintclass" => '',
                "prefixtext" => '',
                "prefixiconname" => '',
                "suffixiconname" => '',
                "suffixtext" => '',
                "placeholder" => '',
                "autocomplete" => 'off',
            ];

            if (count($attributes) != 0) {
                if (!empty($attributes['id'])) $atr->id = $attributes['id'];
                if (!empty($attributes['type'])) $atr->type = $attributes['type'];
                if (!empty($attributes['required'])) $atr->required = $attributes['required'];
                if (!empty($attributes['readonly'])) $atr->readonly = $attributes['readonly'];
                if (!empty($attributes['disabled'])) $atr->disabled = $attributes['disabled'];
                if (!empty($attributes['block_input'])) $atr->block_input = $attributes['block_input'];
                if (!empty($attributes['class'])) $atr->class = $attributes['class'];
                if (!empty($attributes['errormsg'])) $atr->errormsg = $attributes['errormsg'];
                if (!empty($attributes['label'])) $atr->label = $attributes['label'];
                if (!empty($attributes['labelclass'])) $atr->labelclass = $attributes['labelclass'];
                if (!empty($attributes['hint'])) $atr->hint = $attributes['hint'];
                if (!empty($attributes['hintclass'])) $atr->hintclass = $attributes['hintclass'];
                if (!empty($attributes['prefixtext'])) $atr->prefixtext = $attributes['prefixtext'];
                if (!empty($attributes['prefixiconname'])) $atr->prefixiconname = $attributes['prefixiconname'];
                if (!empty($attributes['suffixiconname'])) $atr->suffixiconname = $attributes['suffixiconname'];
                if (!empty($attributes['suffixtext'])) $atr->suffixtext = $attributes['suffixtext'];
                if (!empty($attributes['placeholder'])) $atr->placeholder = $attributes['placeholder'];
                if (!empty($attributes['autocomplete'])) $atr->autocomplete = $attributes['autocomplete'];
            }

            return view('components.form.input', compact('name', 'value', 'atr'));
        } catch (\Exception $e) {
        }
    }

    public static function checkbox($name = '', $value = '', $attributes = [])
    {
        try {
            $atr = (object)[
                "id" => '',
                "required" => '',
                "readonly" => false,
                "class" => '',
                "checked" => false,
            ];

            if (count($attributes) != 0) {
                if (!empty($attributes['id'])) $atr->id = $attributes['id'];
                if (!empty($attributes['type'])) $atr->type = $attributes['type'];
                if (!empty($attributes['required'])) $atr->required = $attributes['required'];
                if (!empty($attributes['readonly'])) $atr->readonly = $attributes['readonly'];
                if (!empty($attributes['class'])) $atr->class = $attributes['class'];
                if (!empty($attributes['checked'])) $atr->checked = $attributes['checked'];
            }

            return view('components.form.checkbox', compact('name', 'value', 'atr'));
        } catch (\Exception $e) {
        }
    }

    public static function radio()
    {
        return view('components.form.radio');
    }

    public static function select()
    {
        return view('components.form.select');
    }
    /**
     * Get the view/fill in the textarea form
     *
     * @param string $name
     * @param string $value
     * @param array $attributes
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public static function textarea($name = '', $value = '', $attributes = [])
    {
        try {
            $atr = (object)[
                "id" => '',
                "rows" => '5',
                "required" => '',
                "class" => '',
                "errormsg" => '',
                "label" => '',
                "labelclass" => '',
                "hint" => '',
                "hintclass" => '',
                "placeholder" => '',
                "autocomplete" => 'off',
            ];

            if (count($attributes) != 0) {
                if (!empty($attributes['id'])) $atr->id = $attributes['id'];
                if (!empty($attributes['rows'])) $atr->rows = $attributes['rows'];
                if (!empty($attributes['required'])) $atr->required = $attributes['required'];
                if (!empty($attributes['class'])) $atr->class = $attributes['class'];
                if (!empty($attributes['errormsg'])) $atr->errormsg = $attributes['errormsg'];
                if (!empty($attributes['label'])) $atr->label = $attributes['label'];
                if (!empty($attributes['labelclass'])) $atr->labelclass = $attributes['labelclass'];
                if (!empty($attributes['hint'])) $atr->hint = $attributes['hint'];
                if (!empty($attributes['hintclass'])) $atr->hintclass = $attributes['hintclass'];
                if (!empty($attributes['placeholder'])) $atr->placeholder = $attributes['placeholder'];
                if (!empty($attributes['autocomplete'])) $atr->autocomplete = $attributes['autocomplete'];
            }

            return view('components.form.textarea', compact('name', 'value', 'atr'));
        } catch (\Exception $e) {
        }
    }

    public static function file()
    {
        return view('components.form.file');
    }

    /**
     * Get the view/fill in the date form
     *
     * @param string $name
     * @param string $value
     * @param array $attributes
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public static function date($name = '', $value = '', $attributes = [])
    {
        try {
            $atr = (object)[
                "id" => '',
                "class" => '',
                "errormsg" => '',
                "label" => '',
                "labelclass" => '',
                "hint" => '',
                "hintclass" => '',
                "prefixtext" => '',
                "prefixiconname" => '',
                "suffixiconname" => '',
                "suffixtext" => '',
                "placeholder" => '',
                "autocomplete" => 'false',
            ];

            if (count($attributes) != 0) {
                if (!empty($attributes['id'])) $atr->id = $attributes['id'];
                if (!empty($attributes['type'])) $atr->type = $attributes['type'];
                if (!empty($attributes['required'])) $atr->required = $attributes['required'];
                if (!empty($attributes['class'])) $atr->class = $attributes['class'];
                if (!empty($attributes['errormsg'])) $atr->errormsg = $attributes['errormsg'];
                if (!empty($attributes['label'])) $atr->label = $attributes['label'];
                if (!empty($attributes['labelclass'])) $atr->labelclass = $attributes['labelclass'];
                if (!empty($attributes['hint'])) $atr->hint = $attributes['hint'];
                if (!empty($attributes['hintclass'])) $atr->hintclass = $attributes['hintclass'];
                if (!empty($attributes['prefixtext'])) $atr->prefixtext = $attributes['prefixtext'];
                if (!empty($attributes['prefixiconname'])) $atr->prefixiconname = $attributes['prefixiconname'];
                if (!empty($attributes['suffixiconname'])) $atr->suffixiconname = $attributes['suffixiconname'];
                if (!empty($attributes['suffixtext'])) $atr->suffixtext = $attributes['suffixtext'];
                if (!empty($attributes['placeholder'])) $atr->placeholder = $attributes['placeholder'];
                if (!empty($attributes['autocomplete'])) $atr->autocomplete = $attributes['autocomplete'];
            }

            return view('components.form.date', compact('name', 'value', 'atr'));
        } catch (\Exception $e) {
        }
    }


    /**
     * Get the view/fill in the textarea form
     *
     * @param string $name
     * @param string $value
     * @param array $list
     * @param array $attributes
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public static function togglebutton($name = '', $value = '', $list = [], $attributes = [])
    {
        try {
            $atr = (object)[
                "id" => '',
                "currentName" => '',
                "class" => '',
                "errormsg" => '',
                "label" => '',
                "labelclass" => '',
                "hint" => '',
                "hintclass" => '',
                "autocomplete" => 'off',
            ];

            if (count($attributes) != 0) {
                if (!empty($attributes['id'])) $atr->id = $attributes['id'];
                if (!empty($attributes['currentName'])) $atr->currentName = $attributes['currentName'];
                if (!empty($attributes['class'])) $atr->class = $attributes['class'];
                if (!empty($attributes['errormsg'])) $atr->errormsg = $attributes['errormsg'];
                if (!empty($attributes['label'])) $atr->label = $attributes['label'];
                if (!empty($attributes['labelclass'])) $atr->labelclass = $attributes['labelclass'];
                if (!empty($attributes['hint'])) $atr->hint = $attributes['hint'];
                if (!empty($attributes['hintclass'])) $atr->hintclass = $attributes['hintclass'];
                if (!empty($attributes['autocomplete'])) $atr->autocomplete = $attributes['autocomplete'];
            }
            return view('components.form.togglebutton', compact('name', 'value', 'list', 'atr'));
        } catch (\Exception $e) {
        }
    }
}
