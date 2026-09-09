<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        $items = DB::table('menu_items')->select('id', 'category_id', 'title')->orderBy('id')->get();
        $used = [];

        foreach ($items as $item) {
            $base = Str::slug((string) $item->title) ?: 'item';
            $slug = $base;
            $suffix = 2;
            $key = $item->category_id.'|'.$slug;

            while (isset($used[$key])) {
                $slug = $base.'-'.$suffix;
                $key = $item->category_id.'|'.$slug;
                $suffix++;
            }

            $used[$key] = true;

            DB::table('menu_items')->where('id', $item->id)->update(['slug' => $slug]);
        }

        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique(['category_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropUnique(['category_id', 'slug']);
            $table->dropColumn('slug');
        });
    }
};
