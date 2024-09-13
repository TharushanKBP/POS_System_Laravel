<?php

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create_products');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'product_code' => ['required', 'string', 'max:255', 'unique:products,product_code'],
            'product_barcode_symbology' => ['required', 'string', 'max:255'],
            'product_unit' => ['required', 'string', 'max:255'],
            'product_quantity' => ['required', 'numeric', 'min:0.00'],
            'product_cost' => ['required', 'numeric', 'max:9999999999999.99'],
            'product_price' => ['required', 'numeric', 'max:9999999999999.99'],
            'product_stock_alert' => ['required', 'numeric', 'min:0.00'], // Changed to numeric for consistency
            'product_order_tax' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'product_tax_type' => ['nullable', 'integer'],
            'product_note' => ['nullable', 'string', 'max:1000'],
            'product_image' => ['nullable', 'array'], // Validate as array if multiple images
            'product_image.*' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'], // Validate each file in the array
            'category_id' => ['required', 'exists:categories,id'], // Ensures the category exists
        ];
    }
}
