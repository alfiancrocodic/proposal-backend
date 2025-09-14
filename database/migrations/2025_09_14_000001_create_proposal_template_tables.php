<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('type')->default('complex');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('proposal_section_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('proposal_sections')->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->string('input_type')->default('text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_checkable')->default(false);
            $table->timestamps();
        });

        Schema::create('proposal_section_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('proposal_sections')->cascadeOnDelete();
            $table->json('values')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('proposal_section_footnotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('proposal_sections')->cascadeOnDelete();
            $table->text('text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('proposal_simple_options', function (Blueprint $table) {
            $table->id();
            $table->string('section_key');
            $table->string('label');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_other')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_simple_options');
        Schema::dropIfExists('proposal_section_footnotes');
        Schema::dropIfExists('proposal_section_rows');
        Schema::dropIfExists('proposal_section_columns');
        Schema::dropIfExists('proposal_sections');
    }
};

