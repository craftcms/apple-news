<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Database\Migrations;

use CraftCms\Cms\Database\Migration;
use CraftCms\Cms\Database\Table;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** @since 2.0 */
class Install extends Migration
{
    public function up(): void
    {
        Schema::create('applenews_articles', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('entryId');
            $table->char('channelId', 36);
            $table->char('articleId', 36)->nullable();
            $table->char('revisionId', 24)->nullable();
            $table->boolean('isSponsored')->default(false);
            $table->boolean('isPreview')->default(false);
            $table->string('state');
            $table->string('shareUrl')->nullable();
            $table->text('response')->nullable();
            $table->dateTime('dateCreated');
            $table->dateTime('dateUpdated');
            $table->char('uid', 36)->default('0');

            $table->unique(['entryId', 'channelId']);
            $table->index(['entryId', 'channelId', 'state']);
            $table->foreign('entryId')->references('id')->on(Table::ENTRIES)->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applenews_articles');
    }
}
