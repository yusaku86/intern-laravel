<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Main extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public $title; // ページのタイトル
    public $css;   // 詠み込むcssファイルのパス

    public function __construct($title, $css)
    {
        $this->title = $title;
        $this->css = $css;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main');
    }
}
