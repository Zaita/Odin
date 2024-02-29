<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\HelpItem;

class HelpItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $sortOrder = 0;
      HelpItem::create([
        "name" => "Help Item One",
        "summary" => "A short summary",
        "content" => "Some Content",
        "sort_order" => $sortOrder++
      ]);

      HelpItem::create([
        "name" => "Help Item Two",
        "summary" => "A short summary",
        "content" => "Some Content",
        "sort_order" => $sortOrder++
      ]);

      HelpItem::create([
        "name" => "Help Item Three",
        "summary" => "A short summary",
        "content" => "Some Content",
        "sort_order" => $sortOrder++
      ]);

      HelpItem::create([
        "name" => "Help Item Four",
        "summary" => "A short summary",
        "content" => "Some Content",
        "sort_order" => $sortOrder++
      ]);

      HelpItem::create([
        "name" => "Help Item Five",
        "summary" => "A short summary",
        "content" => "Some Content",
        "sort_order" => $sortOrder++
      ]);
    }
}
