<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrderExport implements FromView
{
    public $data, $applied_filters;
    public function __construct($data, $applied_filters)
    {
        $this->data = $data;
        $this->applied_filters = $applied_filters;
    }

    public function view(): View
    {
        return view("exports.orders", [
            "data" => $this->data,
            "applied_filters" => $this->applied_filters
        ]);
    }
}
