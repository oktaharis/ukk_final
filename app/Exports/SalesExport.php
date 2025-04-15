<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    protected $filter;
    protected $date;
    protected $search;

    public function __construct($filter, $date, $search)
    {
        $this->filter = $filter;
        $this->date = $date;
        $this->search = $search;
    }

    public function collection()
    {
        return Sale::with(['user', 'products'])
            ->when($this->search, function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('id', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter == 'daily' && $this->date, function ($q) {
                $q->whereDate('created_at', $this->date);
            })
            ->when($this->filter == 'monthly' && $this->date, function ($q) {
                $q->whereMonth('created_at', date('m', strtotime($this->date)))
                  ->whereYear('created_at', date('Y', strtotime($this->date)));
            })
            ->when($this->filter == 'yearly' && $this->date, function ($q) {
                $q->whereYear('created_at', $this->date);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sale) {
                return [
                    'ID' => $sale->id,
                    'Pelanggan' => $sale->customer_name,
                    'Tanggal' => $sale->created_at->format('d/m/Y H:i'),
                    'Total' => 'Rp ' . number_format($sale->total_price, 0, ',', '.'),
                    'Kasir' => $sale->user->name,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Nama Pelanggan',
            'Tanggal Transaksi',
            'Total Harga',
            'Nama Kasir'
        ];
    }
}
