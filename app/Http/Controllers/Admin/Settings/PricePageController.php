<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\BaseController;
use App\Models\PricePage;
use App\Models\Category;
use App\Models\Brand;
use Devrabiul\ToastMagic\Facades\ToastMagic;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Admin Controller for Programmatic SEO Price Pages
 * Manages "[Product] price in Uganda" pages
 */
class PricePageController extends BaseController
{
    /**
     * List all price pages
     */
    public function index(?Request $request = null, ?string $type = null): View|RedirectResponse|null
    {
        $query = PricePage::query();
        
        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('slug', 'LIKE', '%' . $request->search . '%');
        }
        
        $pricePages = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin-views.price-pages.index', compact('pricePages'));
    }

    /**
     * Show create form
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $productTypes = ['tv', 'fridge', 'cooker', 'washing', 'appliance'];
        
        return view('admin-views.price-pages.create', compact('categories', 'brands', 'productTypes'));
    }

    /**
     * Store new price page
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'slug' => 'required|unique:price_pages,slug|regex:/^[a-z0-9-]+$/',
            'title' => 'required|max:70',
            'meta_description' => 'required|max:160',
            'h1' => 'required',
            'intro_text' => 'required',
        ]);

        $data = $request->only([
            'slug', 'title', 'meta_description', 'h1', 'intro_text', 
            'buying_guide', 'category_id', 'brand_id', 'product_type',
            'size_filter', 'feature_filter', 'brand_filter', 'min_price', 'max_price'
        ]);
        
        // Handle FAQs
        if ($request->filled('faq_questions')) {
            $faqs = [];
            foreach ($request->faq_questions as $i => $question) {
                if (!empty($question) && !empty($request->faq_answers[$i])) {
                    $faqs[] = [
                        'question' => $question,
                        'answer' => $request->faq_answers[$i]
                    ];
                }
            }
            $data['faqs'] = json_encode($faqs);
        }
        
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_indexed'] = $request->boolean('is_indexed', true);

        PricePage::create($data);

        ToastMagic::success(translate('Price page created successfully'));
        return redirect()->route('admin.price-pages.index');
    }

    /**
     * Show edit form
     */
    public function edit(int $id): View
    {
        $pricePage = PricePage::findOrFail($id);
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $productTypes = ['tv', 'fridge', 'cooker', 'washing', 'appliance'];
        
        return view('admin-views.price-pages.edit', compact('pricePage', 'categories', 'brands', 'productTypes'));
    }

    /**
     * Update price page
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $pricePage = PricePage::findOrFail($id);

        $request->validate([
            'slug' => 'required|regex:/^[a-z0-9-]+$/|unique:price_pages,slug,' . $id,
            'title' => 'required|max:70',
            'meta_description' => 'required|max:160',
            'h1' => 'required',
            'intro_text' => 'required',
        ]);

        $data = $request->only([
            'slug', 'title', 'meta_description', 'h1', 'intro_text', 
            'buying_guide', 'category_id', 'brand_id', 'product_type',
            'size_filter', 'feature_filter', 'brand_filter', 'min_price', 'max_price'
        ]);
        
        // Handle FAQs
        if ($request->filled('faq_questions')) {
            $faqs = [];
            foreach ($request->faq_questions as $i => $question) {
                if (!empty($question) && !empty($request->faq_answers[$i])) {
                    $faqs[] = [
                        'question' => $question,
                        'answer' => $request->faq_answers[$i]
                    ];
                }
            }
            $data['faqs'] = json_encode($faqs);
        }
        
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_indexed'] = $request->boolean('is_indexed', true);

        $pricePage->update($data);

        ToastMagic::success(translate('Price page updated successfully'));
        return redirect()->route('admin.price-pages.index');
    }

    /**
     * Delete price page
     */
    public function destroy(int $id): RedirectResponse
    {
        $pricePage = PricePage::findOrFail($id);
        $pricePage->delete();

        ToastMagic::success(translate('Price page deleted successfully'));
        return redirect()->route('admin.price-pages.index');
    }

    /**
     * Toggle status
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $pricePage = PricePage::findOrFail($id);
        $pricePage->update(['is_active' => !$pricePage->is_active]);

        $status = $pricePage->is_active ? 'activated' : 'deactivated';
        ToastMagic::success(translate("Price page $status"));
        return redirect()->back();
    }

    /**
     * Preview price page
     */
    public function preview(int $id): View
    {
        $pricePage = PricePage::findOrFail($id);
        $products = $pricePage->getProducts();
        
        return view('admin-views.price-pages.preview', compact('pricePage', 'products'));
    }
}
