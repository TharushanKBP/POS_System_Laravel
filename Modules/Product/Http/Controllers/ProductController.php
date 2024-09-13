<?php

namespace Modules\Product\Http\Controllers;

use Modules\Product\DataTables\ProductDataTable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Modules\Product\Entities\Product;
use Modules\Product\Http\Requests\StoreProductRequest;
use Modules\Product\Http\Requests\UpdateProductRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(ProductDataTable $dataTable)
    {
        abort_if(Gate::denies('access_products'), 403);
        return $dataTable->render('product::products.index');
    }

    public function create()
    {
        abort_if(Gate::denies('create_products'), 403);
        return view('product::products.create');
    }

    public function store(StoreProductRequest $request)
    {
        DB::transaction(function () use ($request) {
            $product = Product::create($request->except('document', 'image'));
            $this->handleDocuments($request, $product);
            $this->handleImages($request, $product);
            toast('Product Created!', 'success');
        });
        return redirect()->route('products.index');
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('show_products'), 403);
        return view('product::products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('edit_products'), 403);
        return view('product::products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($request->except('document', 'image'));
            $this->handleDocuments($request, $product, true);
            $this->handleImages($request, $product);
            toast('Product Updated!', 'info');
        });
        return redirect()->route('products.index');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $product->clearMediaCollection('documents');
            $product->clearMediaCollection('images');
            $product->delete();
            toast('Product Deleted!', 'warning');
        });
        return redirect()->route('products.index');
    }

    protected function handleDocuments(Request $request, Product $product, $isUpdate = false)
    {
        if ($isUpdate) {
            $product->getMedia('documents')
                ->whereNotIn('file_name', $request->input('document', []))
                ->each(fn($media) => $media->delete());
        }

        $existingMedia = $product->getMedia('documents')->pluck('file_name')->toArray();

        foreach ($request->input('document', []) as $file) {
            if (!in_array($file, $existingMedia)) {
                $product->addMedia(Storage::path('temp/dropzone/' . $file))->toMediaCollection('documents');
            }
        }
    }

    // Ensure handleImages handles updates correctly
    protected function handleImages(Request $request, Product $product)
    {
        if ($request->has('image')) {
            $existingMedia = $product->getMedia('images')->pluck('file_name')->toArray();

            // Delete removed images
            $product->getMedia('images')
                ->whereNotIn('file_name', $request->input('image', []))
                ->each(fn($media) => $media->delete());

            // Add new images
            foreach ($request->input('image', []) as $fileName) {
                if (!in_array($fileName, $existingMedia)) {
                    $filePath = Storage::path('temp/dropzone/' . $fileName);
                    $product->addMedia($filePath)->toMediaCollection('images');
                    Storage::delete('temp/dropzone/' . $fileName); // Optionally delete temp files
                }
            }
        }
    }
}
