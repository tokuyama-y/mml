<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('mindscape_results', function (Blueprint $table) {
            $table->id();
            $table->string('uploaded_image_url')->nullable()->comment('アップロード画像のURL');
            $table->string('mindscape_image_url')->nullable()->comment('マインドスケープ画像のURL');
            $table->string('karesansui_image_url')->nullable()->comment('枯山水の画像のURL');
            $table->text('haiku')->nullable()->comment('俳句の文字列');
            $table->string('timelapse_video_url')->nullable()->comment('タイムラプス動画のURL');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mindscape_results');
    }
};
