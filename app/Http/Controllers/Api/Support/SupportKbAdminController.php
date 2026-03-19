<?php

namespace App\Http\Controllers\Api\Support;

use App\Http\Controllers\Controller;
use App\Models\Support\SupportKbArticle;
use App\Models\Support\SupportKbCategory;
use App\Models\Support\SupportKbArticleVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportKbAdminController extends Controller
{
    /**
     * Manage articles (CRUD).
     */
    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:support_kb_categories,id',
            'is_public' => 'boolean',
            'is_published' => 'boolean',
        ]);

        $article = SupportKbArticle::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(5),
            'content' => $request->content,
            'excerpt' => Str::limit(strip_tags($request->content), 150),
            'category_id' => $request->category_id,
            'author_id' => auth()->id(),
            'is_public' => $request->is_public ?? true,
            'is_published' => $request->is_published ?? false,
            'published_at' => ($request->is_published ?? false) ? now() : null,
        ]);

        return response()->json($article, 201);
    }

    public function updateArticle(Request $request, $id)
    {
        $article = SupportKbArticle::findOrFail($id);

        $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
            'category_id' => 'exists:support_kb_categories,id',
        ]);

        // Create version before update
        SupportKbArticleVersion::create([
            'article_id' => $article->id,
            'title' => $article->getAttribute('title'),
            'content' => $article->getAttribute('content'),
            'version_number' => $article->version,
            'created_by' => auth()->id(),
            'change_note' => $request->change_note ?? 'Update',
        ]);

        $article->update($request->only(['title', 'content', 'category_id', 'is_public', 'is_published']));
        $article->increment('version');

        if ($request->is_published && !$article->published_at) {
            $article->update(['published_at' => now()]);
        }

        return response()->json($article);
    }

    public function destroyArticle($id)
    {
        SupportKbArticle::findOrFail($id)->delete();
        return response()->json(null, 204);
    }

    /**
     * Manage categories.
     */
    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:support_kb_categories,name']);

        $category = SupportKbCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_public' => $request->is_public ?? true,
        ]);

        return response()->json($category, 201);
    }
}
