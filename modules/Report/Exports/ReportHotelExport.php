<?php

namespace Modules\Report\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class ReportHotelExport implements  FromView, ShouldAutoSize
{
    use Exportable;

    public function records($records) {
        $this->records = $records;

        return $this;
    }

    public function company($company) {
        $this->company = $company;

        return $this;
    }

    public function rooms($rooms) {
        $this->rooms = $rooms;

        return $this;
    }


    public function view(): View {
        return view('report::report_hotels.report_excel', [
            'records'=> $this->records,
            'company' => $this->company,
            'rooms' => $this->rooms,
        ]);
    }
}
