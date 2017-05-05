<template>

<div>
    <h3 class="subtitle">第{{ titleText }}文</h3>
    <div class="form-group form-inline">
        <label :id="labelId" class="control-label">最後の単語:</label>
        {{ wordFirstLetter }}<input :id="inputId" type="text" class="" v-model="answer2" v-on:input="onAnswerChanged">
    </div>
</div>

</template>


<script>
require("../../helpers");

export default {
    data () {
        return {
            answer1: null,
            answer2: ""
        }
    },
    props: {
        index: [Number],
        word: String,
        idPrefix: {
            type: String,
            default: function () {
                return Helpers.getUniqueStr();
            }
        }
    },
    computed: {
        wordFirstLetter() {
            return this.word[0];
        },
        labelId() {
            return this.idPrefix + '_label';
        },
        inputId() {
            return this.idPrefix + '_input';
        },
        titleText() {
            return this.index + 1;
        }
    },
    methods: {
        onAnswerChanged() {
            this.$emit('answer-changed', this.index, {
                word: this.wordFirstLetter + this.answer2
            });
        }
    },
    mounted() {
        this.onAnswerChanged();
    }
}

</script>