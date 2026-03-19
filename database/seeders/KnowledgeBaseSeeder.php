<?php

namespace Database\Seeders;

use App\Models\Support\SupportKbCategory;
use App\Models\Support\SupportKbArticle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KnowledgeBaseSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first(); // Assumes at least one user exists

        // 1. Getting Started Category
        $gettingStarted = SupportKbCategory::create([
            'name' => 'Getting Started',
            'slug' => 'getting-started',
            'description' => 'Essential guides for new users',
            'icon' => 'book-open',
            'sort_order' => 1
        ]);

        $this->createArticle(
            $gettingStarted->id,
            $author->id,
            'Platform Overview',
            'Welcome to our enterprise platform. This guide covers navigation, account settings, and core features.',
            'platform-overview'
        );

        $this->createArticle(
            $gettingStarted->id,
            $author->id,
            'Setting Up Your Profile',
            'Learn how to customize your profile, set up two-factor authentication, and manage notification preferences.',
            'profile-setup'
        );

        // 2. Technical Support Category
        $techSupport = SupportKbCategory::create([
            'name' => 'Technical Support',
            'slug' => 'technical-support',
            'description' => 'Troubleshooting and advanced configuration',
            'icon' => 'cpu',
            'sort_order' => 2
        ]);

        $this->createArticle(
            $techSupport->id,
            $author->id,
            'Troubleshooting Login Issues',
            'Common solutions for login problems, password resets, and account lockouts.',
            'login-troubleshooting'
        );

        $this->createArticle(
            $techSupport->id,
            $author->id,
            'API Integration Guide',
            'Detailed documentation for integrating our services via the REST API.',
            'api-guide'
        );

        // 3. Billing & Payments Category
        $billing = SupportKbCategory::create([
            'name' => 'Billing & Payments',
            'slug' => 'billing-payments',
            'description' => 'Invoices, subscriptions, and payment methods',
            'icon' => 'credit-card',
            'sort_order' => 3
        ]);

        $this->createArticle(
            $billing->id,
            $author->id,
            'Understanding Your Invoice',
            'A breakdown of how to read your monthly billing statement and service charges.',
            'understanding-invoices'
        );
    }

    protected function createArticle($catId, $authorId, $title, $content, $slug)
    {
        return SupportKbArticle::create([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => Str::limit($content, 100),
            'category_id' => $catId,
            'author_id' => $authorId,
            'is_published' => true,
            'is_public' => true,
            'published_at' => now(),
        ]);
    }
}
