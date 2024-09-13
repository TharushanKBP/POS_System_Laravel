<?php

namespace Modules\Adjustment\Http\Controllers;

use Modules\Adjustment\DataTables\AdjustmentsDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Modules\Adjustment\Entities\AdjustedProduct;
use Modules\Adjustment\Entities\Adjustment;
use Modules\Product\Entities\Product;

class AdjustmentController extends Controller
{
    public function index(AdjustmentsDataTable $dataTable)
    {
        abort_if(Gate::denies('access_adjustments'), 403);

        return $dataTable->render('adjustment::index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        return view('adjustment::create');
    }

    public function store(Request $request)
    {
        abort_if(Gate::denies('create_adjustments'), 403);

        $validated = $request->validate([
            'reference'   => 'required|string|max:255',
            'date'        => 'required|date',
            'note'        => 'nullable|string|max:1000',
            'product_ids' => 'required|array',
            'quantities'  => 'required|array',
            'types'       => 'required|array'
        ]);

        DB::transaction(function () use ($validated) {
            $adjustment = Adjustment::create([
                'date' => $validated['date'],
                'note' => $validated['note']
            ]);

            foreach ($validated['product_ids'] as $key => $id) {
                $quantity = $validated['quantities'][$key];
                $type = $validated['types'][$key];

                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $id,
                    'quantity'      => $quantity,
                    'type'          => $type
                ]);

                $product = Product::findOrFail($id);
                $newQuantity = ($type == 'add')
                    ? $product->product_quantity + $quantity
                    : $product->product_quantity - $quantity;

                $product->update(['product_quantity' => $newQuantity]);
            }
        });

        toast('Adjustment Created!', 'success');

        return redirect()->route('adjustments.index');
    }

    public function show(Adjustment $adjustment)
    {
        abort_if(Gate::denies('show_adjustments'), 403);

        return view('adjustment::show', compact('adjustment'));
    }

    public function edit(Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        return view('adjustment::edit', compact('adjustment'));
    }

    public function update(Request $request, Adjustment $adjustment)
    {
        abort_if(Gate::denies('edit_adjustments'), 403);

        $validated = $request->validate([
            'reference'   => 'required|string|max:255',
            'date'        => 'required|date',
            'note'        => 'nullable|string|max:1000',
            'product_ids' => 'required|array',
            'quantities'  => 'required|array',
            'types'       => 'required|array'
        ]);

        DB::transaction(function () use ($validated, $adjustment) {
            $adjustment->update([
                'reference' => $validated['reference'],
                'date'      => $validated['date'],
                'note'      => $validated['note']
            ]);

            // Reverse the effect of previous adjustment
            foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                $product = Product::findOrFail($adjustedProduct->product_id);
                $reverseQuantity = ($adjustedProduct->type == 'add')
                    ? $product->product_quantity - $adjustedProduct->quantity
                    : $product->product_quantity + $adjustedProduct->quantity;

                $product->update(['product_quantity' => $reverseQuantity]);
                $adjustedProduct->delete();
            }

            // Apply the new adjustment
            foreach ($validated['product_ids'] as $key => $id) {
                $quantity = $validated['quantities'][$key];
                $type = $validated['types'][$key];

                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $id,
                    'quantity'      => $quantity,
                    'type'          => $type
                ]);

                $product = Product::findOrFail($id);
                $newQuantity = ($type == 'add')
                    ? $product->product_quantity + $quantity
                    : $product->product_quantity - $quantity;

                $product->update(['product_quantity' => $newQuantity]);
            }
        });

        toast('Adjustment Updated!', 'info');

        return redirect()->route('adjustments.index');
    }

    public function destroy(Adjustment $adjustment)
    {
        abort_if(Gate::denies('delete_adjustments'), 403);

        DB::transaction(function () use ($adjustment) {
            foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                $product = Product::findOrFail($adjustedProduct->product_id);
                $reverseQuantity = ($adjustedProduct->type == 'add')
                    ? $product->product_quantity - $adjustedProduct->quantity
                    : $product->product_quantity + $adjustedProduct->quantity;

                $product->update(['product_quantity' => $reverseQuantity]);
                $adjustedProduct->delete();
            }

            $adjustment->delete();
        });

        toast('Adjustment Deleted!', 'warning');

        return redirect()->route('adjustments.index');
    }
}
