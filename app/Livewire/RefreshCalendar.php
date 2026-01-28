<?php

namespace App\Livewire;

use Livewire\Component;

class RefreshCalendar extends Component
{
    public function refresh()
    {
        $this->dispatch('refreshCalendar');
    }

    public function render()
    {
        return view('livewire.refresh-calendar');
    }
}