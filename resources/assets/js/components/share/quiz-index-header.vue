<template>
<div class="page-header">
    <h1>第{{ index }}問<small> {{ questionNumText }}</small></h1>
</div>
</template>

<script>
export default {
    props: ["index", "questionNum"],
    computed: {
        questionNumText() {
            if (this.questionNum !== undefined) {
                return "計" + this.questionNum + "文連続";
            }
            return "";
        }
    }
}
</script>
