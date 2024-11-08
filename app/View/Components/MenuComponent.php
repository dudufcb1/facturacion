<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MenuComponent extends Component
{
  public $title;
  public $routeIndex;
  public $routeCreate;

  public function __construct($title, $routeIndex, $routeCreate)
  {
    $this->title = $title;
    $this->routeIndex = $routeIndex;
    $this->routeCreate = $routeCreate;
  }

  public function render(): View|Closure|string
  {
    return view('components.menu-component');
  }
}
