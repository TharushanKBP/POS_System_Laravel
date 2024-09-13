<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\Product;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ProductDataTable extends DataTable
{
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('image', function ($data) {
                $url = $data->getFirstMediaUrl('images', 'thumb'); // Get the thumbnail URL of the product image
                return $url
                    ? '<img src="' . $url . '" border="0" width="50" class="img-thumbnail" align="center"/>'
                    : '<span class="text-muted">No Image</span>';
            })
            ->addColumn('action', function ($data) {
                return view('product::products.partials.actions', compact('data'));
            })
            ->rawColumns(['image', 'action']); // Ensure the image and action columns are treated as raw HTML
    }

    public function query(Product $model)
    {
        return $model->newQuery()->with('category'); // Ensure the category is loaded with the product
    }

    public function html()
    {
        return $this->builder()
            ->setTableId('product-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                'tr' .
                                <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(7)
            ->buttons(
                Button::make('excel')->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')->text('<i class="bi bi-arrow-repeat"></i> Reload')
            );
    }

    protected function getColumns()
    {
        return [
            Column::computed('image')
                ->title('Image')
                ->className('text-center align-middle'),

            Column::make('category.category_name')
                ->title('Category')
                ->className('text-center align-middle'),

            Column::make('product_code')
                ->title('Code')
                ->className('text-center align-middle'),

            Column::make('product_name')
                ->title('Name')
                ->className('text-center align-middle'),

            Column::make('product_cost')
                ->title('Cost')
                ->className('text-center align-middle'),

            Column::make('product_price')
                ->title('Price')
                ->className('text-center align-middle'),

            Column::make('product_quantity')
                ->title('Quantity')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')->visible(false) // This column will be hidden in the DataTable
        ];
    }

    protected function filename(): string
    {
        return 'Product_' . date('YmdHis');
    }
}
