<!--<template>-->
<!--    <div class="mindscape-grid">-->
<!--        <div-->
<!--            v-for="(item, index) in items"-->
<!--            :key="index"-->
<!--            class="mindscape-card"-->
<!--        >-->
<!--            &lt;!&ndash; アップロード画像 &ndash;&gt;-->
<!--            <div class="media-box">-->
<!--                <img :src="item.uploaded_image_url" alt="Uploaded" class="media-img" />-->
<!--            </div>-->

<!--            &lt;!&ndash; 心象風景SVG &ndash;&gt;-->
<!--            <div v-if="item.mindscape_image_url" class="media-box">-->
<!--                <img-->
<!--                    :src="item.mindscape_image_url"-->
<!--                    alt="Karesansui"-->
<!--                    class="media-img contain"-->
<!--                />-->
<!--            </div>-->

<!--            &lt;!&ndash; 枯山水画像 &ndash;&gt;-->
<!--            <div v-if="item.karesansui_image_url" class="media-box karesansui-box">-->
<!--                <img-->
<!--                    :src="item.karesansui_image_url"-->
<!--                    alt="Karesansui"-->
<!--                    class="media-img contain"-->
<!--                />-->
<!--            </div>-->

<!--            &lt;!&ndash; 俳句 &ndash;&gt;-->
<!--            <div class="haiku-box">-->
<!--                <p class="haiku-label">Haiku</p>-->
<!--                <pre class="haiku-text">-->
<!--{{ item.haiku }}-->
<!--        </pre>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</template>-->

<!--<script setup lang="ts">-->
<!--import { toRefs } from "vue";-->

<!--const props = defineProps<{-->
<!--    items: {-->
<!--        uploaded_image_url: string;-->
<!--        mindscape_image_url: string;-->
<!--        karesansui_image_url: string;-->
<!--        haiku: string;-->
<!--    }[];-->
<!--}>();-->

<!--const { name } = toRefs(props);-->
<!--</script>-->

<!--<style scoped>-->
<!--/* グリッド全体 */-->
<!--.mindscape-grid {-->
<!--    display: grid;-->
<!--    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));-->
<!--    gap: 24px;-->
<!--    padding: 16px;-->
<!--}-->

<!--/* 各カードのデザイン */-->
<!--.mindscape-card {-->
<!--    background: white;-->
<!--    padding: 16px;-->
<!--    border-radius: 16px;-->
<!--    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);-->
<!--    transition: box-shadow 0.3s ease;-->
<!--}-->
<!--.mindscape-card:hover {-->
<!--    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);-->
<!--}-->

<!--/* メディアボックス（画像やSVGの枠） */-->
<!--.media-box {-->
<!--    width: 100%;-->
<!--    aspect-ratio: 4 / 3;-->
<!--    border: 1px solid #ccc;-->
<!--    border-radius: 12px;-->
<!--    overflow: hidden;-->
<!--    background-color: white;-->
<!--    margin-bottom: 12px;-->
<!--}-->
<!--.karesansui-box {-->
<!--    background-color: #f1f1f1;-->
<!--}-->

<!--/* 画像共通スタイル */-->
<!--.media-img {-->
<!--    width: 100%;-->
<!--    height: 100%;-->
<!--    object-fit: cover;-->
<!--}-->
<!--.media-img.contain {-->
<!--    object-fit: contain;-->
<!--}-->

<!--/* 俳句セクション */-->
<!--.haiku-box {-->
<!--    color: #333;-->
<!--}-->
<!--.haiku-label {-->
<!--    font-size: 14px;-->
<!--    font-weight: 600;-->
<!--    color: #666;-->
<!--    margin-bottom: 4px;-->
<!--}-->
<!--.haiku-text {-->
<!--    font-family: 'Georgia', serif;-->
<!--    font-size: 16px;-->
<!--    line-height: 1.5;-->
<!--    white-space: pre-wrap;-->
<!--    color: #444;-->
<!--}-->
<!--</style>-->


<!--<template>-->
<!--    <div class="mindscape-list">-->
<!--        <div-->
<!--            v-for="(item, index) in items"-->
<!--            :key="index"-->
<!--            class="mindscape-card"-->
<!--        >-->
<!--            &lt;!&ndash; アップロード画像 &ndash;&gt;-->
<!--            <div class="media-box">-->
<!--                <img :src="item.uploaded_image_url" alt="Uploaded" class="media-img" />-->
<!--            </div>-->

<!--            &lt;!&ndash; 心象風景 &ndash;&gt;-->
<!--            <div v-if="item.mindscape_image_url" class="media-box">-->
<!--                <div v-html="item.mindscape_image_url" class="media-img" />-->
<!--            </div>-->

<!--            &lt;!&ndash; 枯山水 SVG &ndash;&gt;-->
<!--            <div v-if="item.karesansui_image_url" class="media-box">-->
<!--                <div v-html="item.karesansui_image_url" class="media-img" />-->
<!--            </div>-->

<!--            &lt;!&ndash; 俳句 &ndash;&gt;-->
<!--            <div class="haiku-box">-->
<!--                <p class="haiku-label">Haiku</p>-->
<!--                <pre class="haiku-text">{{ item.haiku }}</pre>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</template>-->

<!--<script setup lang="ts">-->
<!--defineProps<{-->
<!--    items: {-->
<!--        uploaded_image_url: string;-->
<!--        mindscape_image_url: string;-->
<!--        karesansui_image_url: string;-->
<!--        haiku: string;-->
<!--    }[]-->
<!--}>();-->
<!--</script>-->

<!--<style scoped>-->
<!--.mindscape-list {-->
<!--    display: flex;-->
<!--    flex-direction: column;-->
<!--    gap: 32px;-->
<!--    padding: 16px;-->
<!--}-->

<!--.mindscape-card {-->
<!--    background: #fff;-->
<!--    border-radius: 16px;-->
<!--    box-shadow: 0 4px 12px rgba(0,0,0,0.08);-->
<!--    padding: 24px;-->
<!--    max-width: 100%; /* アイテム全体幅にする */-->
<!--}-->

<!--.media-box {-->
<!--    width: 100%;-->
<!--    aspect-ratio: 4 / 3;-->
<!--    border: 1px solid #ccc;-->
<!--    border-radius: 12px;-->
<!--    overflow: hidden;-->
<!--    margin-bottom: 12px;-->
<!--    background-color: #f9f9f9;-->
<!--}-->

<!--.media-img {-->
<!--    width: 100%;-->
<!--    height: 100%;-->
<!--    object-fit: contain;-->
<!--}-->

<!--.haiku-box {-->
<!--    color: #333;-->
<!--}-->

<!--.haiku-label {-->
<!--    font-size: 14px;-->
<!--    font-weight: bold;-->
<!--    margin-bottom: 4px;-->
<!--}-->

<!--.haiku-text {-->
<!--    font-family: 'Georgia', serif;-->
<!--    font-size: 18px;-->
<!--    line-height: 1.6;-->
<!--    white-space: pre-wrap;-->
<!--}-->
<!--</style>-->

<template>
    <div class="mindscape-list">
        <div
            v-for="(item, index) in items"
            :key="index"
            class="mindscape-card"
        >
            <!-- アップロード画像 -->
            <div class="media-box">
                <img :src="item.uploaded_image_url" alt="Uploaded" class="media-img" />
            </div>

            <!-- 心象風景（SVG埋め込みまたは画像） -->
            <div v-if="item.mindscape_image_url" class="media-box">
                <div
                    v-if="item.mindscape_image_url.startsWith('<svg')"
                    v-html="item.mindscape_image_url"
                    class="media-img"
                />
                <img
                    v-else
                    :src="item.mindscape_image_url"
                    alt="Mindscape"
                    class="media-img"
                />
            </div>

            <!-- 枯山水（SVG埋め込みまたは画像） -->
            <div v-if="item.karesansui_image_url" class="media-box karesansui-box">
                <div
                    v-if="item.karesansui_image_url.startsWith('<svg')"
                    v-html="item.karesansui_image_url"
                    class="media-img"
                />
                <img
                    v-else
                    :src="item.karesansui_image_url"
                    alt="Karesansui"
                    class="media-img"
                />
            </div>

            <!-- 俳句 -->
            <div class="haiku-box">
                <p class="haiku-label">Haiku</p>
                <pre class="haiku-text">{{ item.haiku }}</pre>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    items: {
        uploaded_image_url: string;
        mindscape_image_url: string;
        karesansui_image_url: string;
        haiku: string;
    }[];
}>();
</script>

<style scoped>
.mindscape-list {
    display: flex;
    flex-direction: column;
    gap: 32px;
    padding: 24px;
}

.mindscape-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    padding: 24px;
    width: 100%;
    max-width: 960px;
    margin: 0 auto;
}

.media-box {
    width: 100%;
    aspect-ratio: 4 / 3;
    border: 1px solid #ccc;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 12px;
    background-color: #f9f9f9;
}

.karesansui-box {
    background-color: #eef1f5;
}

.media-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.haiku-box {
    color: #333;
}

.haiku-label {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 4px;
}

.haiku-text {
    font-family: 'Georgia', serif;
    font-size: 18px;
    line-height: 1.6;
    white-space: pre-wrap;
    color: #444;
}
</style>
