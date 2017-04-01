<template>
<div class="loader"></div>
</template>

<script>
</script>

<style scoped>
.loader
{
    width: 30px; /*ローディングアイコンの横サイズ*/
    height: 30px; /*ローディングアイコンの縦サイズ*/
    border-radius: 50%;  /*CSS3で円を書く*/
    border: 8px solid lightblue;  /*円に○の白枠をつける*/
    border-right-color: transparent; /*円の右にC状の空きをつける*/

    animation: spin 1s linear infinite;
}

@keyframes spin
{
    0% { transform: rotate(0deg);   opacity: 0.2; } /*0%の時は20％の透明度*/
    50%  { transform: rotate(180deg); opacity: 1.0; } /*50%の時は透明度なし*/
    100%   { transform: rotate(360deg); opacity: 0.2; } /*100%の時に20％の透明度に戻る*/
}
</style>