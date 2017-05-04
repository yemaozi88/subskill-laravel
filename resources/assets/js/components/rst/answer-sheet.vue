<template>

<div>
    <h3 class="subtitle">第{{ titleText }}文</h3>
    <div class="form-group form-inline">
        <label :id="labelId" class="control-label">(1)文章の内容:</label>
        <div class="radio">
            <label>
                <input type="radio" :name="radioName" value="1" v-model="answer1" v-on:click="onAnswerChanged">
                正しい
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" :name="radioName" value="2" v-model="answer1" v-on:click="onAnswerChanged">
                誤り
            </label>
        </div>
    </div>
    <div class="form-group form-inline">
        <label :id="labelId" class="control-label">(2)最後の単語:</label>
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
        radioName() {
            return this.idPrefix + '_radio';
        },
        titleText() {
            return this.index + 1;
        }
    },
    methods: {
        onAnswerChanged() {
            this.$emit('answer-changed', this.index, {
                word: this.wordFirstLetter + this.answer2,
                judgement: this.answer1 === null ? null : (this.answer1 == "1")
            });
        }
    }
}

</script>