<?php

namespace App\Livewire\Barcode;

use Livewire\Component;
use Milon\Barcode\Facades\DNS1DFacade;
use Modules\Product\Entities\Product;

class ProductTable extends Component
{
    public $product;
    public $quantity;
    public $barcodes;

    protected $listeners = ['productSelected'];

    public function mount()
    {
        $this->product = '';
        $this->quantity = 0;
        $this->barcodes = [];
    }

    public function render()
    {
        return view('livewire.barcode.product-table');
    }

    public function productSelected(Product $product)
    {
        $this->product = $product;
        $this->quantity = 1;
        $this->barcodes = [];
    }

    public function generateBarcodes(Product $product, $quantity)
    {
        if ($quantity > 100) {
            return session()->flash('message', 'Max quantity is 100 per barcode generation!');
        }

        if (!is_numeric($product->product_code)) {
            return session()->flash('message', 'Can not generate Barcode with this type of Product Code');
        }

        $this->barcodes = [];

        for ($i = 1; $i <= $quantity; $i++) {
            $barcode = DNS1DFacade::getBarCodeSVG($product->product_code, $product->product_barcode_symbology, 2, 60, 'black', false);
            array_push($this->barcodes, $barcode);
        }
    }

    public function getPdf()
    {
        // Check if barcodes have been generated
        if (empty($this->barcodes)) {
            return session()->flash('message', 'No barcodes generated yet.');
        }

        // Load the view containing the barcodes into a PDF
        $pdf = \PDF::loadView('product::barcode.print', [
            'barcodes' => $this->barcodes,
            'price' => $this->product->product_price,
            'name' => $this->product->product_name,
        ]);

        // Generate a filename for the PDF
        $filename = 'barcodes-' . $this->product->product_code . '.pdf';

        // Stream the PDF content directly to the browser for download
        return $pdf->stream($filename);
    }



    public function updatedQuantity()
    {
        $this->barcodes = [];
    }
}
