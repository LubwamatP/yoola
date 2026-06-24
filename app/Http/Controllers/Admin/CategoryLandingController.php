<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\FileManagerTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Category Landing Page Controller
 * 
 * Manages enhanced category landing pages with:
 * - Hero sections
 * - Value propositions (Hormozi-style)
 * - Trust badges
 * - FAQs for SEO
 * - Custom content blocks
 * - Conversion elements
 */
class CategoryLandingController extends Controller
{
    use FileManagerTrait;

    /**
     * List all categories with landing page status
     */
    public function index(Request $request)
    {
        $query = Category::where('position', 0) // Main categories only
            ->withCount(['product as active_products_count' => function ($q) {
                $q->where('status', 1);
            }])
            ->with(['seo']);

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $categories = $query->orderBy('priority', 'asc')->paginate(20);

        return view('admin-views.category-landing.index', compact('categories'));
    }

    /**
     * Edit category landing page
     */
    public function edit($id)
    {
        $category = Category::with(['seo', 'childes'])->findOrFail($id);
        
        // Default trust badges
        $defaultTrustBadges = [
            ['icon' => 'bi-shield-check', 'text' => 'Genuine Products', 'active' => true],
            ['icon' => 'bi-truck', 'text' => 'Fast Delivery', 'active' => true],
            ['icon' => 'bi-arrow-repeat', 'text' => 'Easy Returns', 'active' => true],
            ['icon' => 'bi-headset', 'text' => '24/7 Support', 'active' => true],
            ['icon' => 'bi-credit-card', 'text' => 'Secure Payment', 'active' => false],
            ['icon' => 'bi-cash', 'text' => 'Cash on Delivery', 'active' => true],
        ];

        // Default value props
        $defaultValueProps = [
            ['icon' => 'bi-star-fill', 'title' => 'Best Prices', 'description' => 'Competitive prices in Uganda'],
            ['icon' => 'bi-patch-check', 'title' => 'Warranty Included', 'description' => 'All products come with warranty'],
            ['icon' => 'bi-lightning', 'title' => 'Fast Shipping', 'description' => 'Delivery within Kampala in 24hrs'],
        ];

        $trustBadges = $category->trust_badges ?? $defaultTrustBadges;
        $valueProps = $category->value_props ?? $defaultValueProps;
        $faqs = $category->faqs ?? [];

        return view('admin-views.category-landing.edit', compact(
            'category', 'trustBadges', 'valueProps', 'faqs'
        ));
    }

    /**
     * Update category landing page
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'layout_type' => 'required|in:standard,landing,minimal',
        ]);

        $category = Category::findOrFail($id);

        DB::beginTransaction();
        try {
            // Handle hero image upload
            $heroImage = $category->hero_image;
            if ($request->hasFile('hero_image')) {
                // Delete old image
                if ($heroImage) {
                    $this->delete('category/hero/' . $heroImage);
                }
                $heroImage = $this->upload('category/hero/', 'webp', $request->file('hero_image'));
            }

            // Process FAQs
            $faqs = [];
            if ($request->has('faq_question') && is_array($request->faq_question)) {
                foreach ($request->faq_question as $index => $question) {
                    if (!empty($question) && !empty($request->faq_answer[$index])) {
                        $faqs[] = [
                            'question' => $question,
                            'answer' => $request->faq_answer[$index],
                        ];
                    }
                }
            }

            // Process Value Props
            $valueProps = [];
            if ($request->has('value_prop_title') && is_array($request->value_prop_title)) {
                foreach ($request->value_prop_title as $index => $title) {
                    if (!empty($title)) {
                        $valueProps[] = [
                            'icon' => $request->value_prop_icon[$index] ?? 'bi-star',
                            'title' => $title,
                            'description' => $request->value_prop_description[$index] ?? '',
                        ];
                    }
                }
            }

            // Process Trust Badges
            $trustBadges = [];
            if ($request->has('trust_badge_text') && is_array($request->trust_badge_text)) {
                foreach ($request->trust_badge_text as $index => $text) {
                    if (!empty($text)) {
                        $trustBadges[] = [
                            'icon' => $request->trust_badge_icon[$index] ?? 'bi-check-circle',
                            'text' => $text,
                            'active' => isset($request->trust_badge_active[$index]),
                        ];
                    }
                }
            }

            $category->update([
                // Hero Section
                'hero_title' => $request->hero_title,
                'hero_subtitle' => $request->hero_subtitle,
                'hero_image' => $heroImage,
                'hero_cta_text' => $request->hero_cta_text ?? 'Shop Now',
                'hero_cta_link' => $request->hero_cta_link,
                
                // Value Props
                'value_prop_headline' => $request->value_prop_headline,
                'value_props' => $valueProps,
                
                // Trust Badges
                'trust_badges' => $trustBadges,
                
                // Content
                'content_top' => $request->content_top,
                'content_bottom' => $request->content_bottom,
                
                // FAQs
                'faqs' => $faqs,
                
                // Buying Guide
                'buying_guide_title' => $request->buying_guide_title,
                'buying_guide' => $request->buying_guide,
                
                // Social Proof
                'review_highlight' => $request->review_highlight,
                'review_count_display' => $request->review_count_display,
                'avg_rating_display' => $request->avg_rating_display,
                
                // Conversion Elements
                'urgency_text' => $request->urgency_text,
                'promo_banner_text' => $request->promo_banner_text,
                'promo_banner_color' => $request->promo_banner_color ?? '#dc3545',
                
                // WhatsApp
                'whatsapp_message' => $request->whatsapp_message,
                'show_whatsapp_float' => $request->has('show_whatsapp_float'),
                
                // Layout
                'layout_type' => $request->layout_type,
                'products_per_page' => $request->products_per_page ?? 20,
                'default_sort' => $request->default_sort ?? 'popularity',
                
                // Toggle
                'use_landing_page' => $request->has('use_landing_page'),
            ]);

            // Update SEO Meta
            if ($request->filled('meta_title') || $request->filled('meta_description')) {
                $category->seo()->updateOrCreate(
                    ['seoable_id' => $category->id, 'seoable_type' => Category::class],
                    [
                        'title' => $request->meta_title,
                        'description' => $request->meta_description,
                        'index' => $request->meta_index ?? 'index',
                        'no_follow' => $request->meta_no_follow ? 'nofollow' : null,
                    ]
                );
            }

            DB::commit();
            Toastr::success(translate('Landing page updated successfully'));
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Toastr::error(translate('Something went wrong: ') . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Quick toggle landing page on/off
     */
    public function toggleLandingPage($id)
    {
        $category = Category::findOrFail($id);
        $category->use_landing_page = !$category->use_landing_page;
        $category->save();

        return response()->json([
            'success' => true,
            'status' => $category->use_landing_page,
            'message' => $category->use_landing_page
                ? translate('Landing page enabled')
                : translate('Landing page disabled')
        ]);
    }

    /**
     * Preview landing page
     */
    public function preview($id)
    {
        $category = Category::with(['seo', 'childes'])->findOrFail($id);
        
        // Force landing page view for preview
        $category->use_landing_page = true;
        
        return redirect()->route('category-products', $category->slug)
            ->with('preview_mode', true);
    }

    /**
     * Duplicate landing page settings to another category
     */
    public function duplicate(Request $request, $id)
    {
        $request->validate([
            'target_category_id' => 'required|exists:categories,id'
        ]);

        $source = Category::findOrFail($id);
        $target = Category::findOrFail($request->target_category_id);

        $target->update([
            'hero_title' => str_replace($source->name, $target->name, $source->hero_title ?? ''),
            'hero_subtitle' => $source->hero_subtitle,
            'hero_cta_text' => $source->hero_cta_text,
            'value_prop_headline' => $source->value_prop_headline,
            'value_props' => $source->value_props,
            'trust_badges' => $source->trust_badges,
            'content_top' => $source->content_top,
            'content_bottom' => $source->content_bottom,
            'buying_guide_title' => $source->buying_guide_title,
            'buying_guide' => $source->buying_guide,
            'promo_banner_text' => $source->promo_banner_text,
            'promo_banner_color' => $source->promo_banner_color,
            'whatsapp_message' => str_replace($source->name, $target->name, $source->whatsapp_message ?? ''),
            'show_whatsapp_float' => $source->show_whatsapp_float,
            'layout_type' => $source->layout_type,
            'products_per_page' => $source->products_per_page,
            'default_sort' => $source->default_sort,
        ]);

        Toastr::success(translate('Landing page settings duplicated successfully'));
        return redirect()->route('admin.category-landing.edit', $target->id);
    }

    /**
     * Bulk enable landing pages
     */
    public function bulkEnable(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No categories selected']);
        }

        Category::whereIn('id', $ids)->update(['use_landing_page' => true]);

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' ' . translate('categories enabled')
        ]);
    }
}
