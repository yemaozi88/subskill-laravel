<template>

<div>
    <div class="form-group">
        <label :for="inputId">数字({{ question.num }}個)を{{ orderText }}入力してください(半角)</label>
        <input type="text" class="form-control" :id="inputId" placeholder="Selected numbers" 
                :maxlength="question.num" v-model="answer" v-on:input="onAnswerChanged"
                v-on:keypress="onKeyPressed($event)">
    </div>
</div>

</template>

<script>
import _ from "lodash";
require("../../helpers.js")

export default {
    data () {
        return {
            answer: ""
        }
    },
    props: {
        idPrefix: {
            type: String,
            default: function () {
                return Helpers.getUniqueStr();
            }
        },
        /**
         * Example:
         * {num: 2, order: "F", sequence: [1, 2], urls: ["1.wav", "2.wav"]}
         */
        question: {
            type: Object,
            required: true
        }
    },
    computed: {
        inputId() {
            return this.idPrefix + '_input';
        },
        orderText () {
            return this.question.order == "F" ? "聞いた順番通りに" : "聞いた順番と逆順に";
        }
    },
    methods: {
        onAnswerChanged() {
            var userAnswer = _.map(this.answer, x => parseInt(x));
            var correctAnswer = _.clone(this.question.sequence);
            if (this.question.order != "F") {
                correctAnswer.reverse();
            }
            var isAnswerCorrect = _.isEqual(userAnswer, correctAnswer);
            this.$emit("answer-changed", { userAnswer, isAnswerCorrect });
        },
        onKeyPressed(event) {
            if (!(event.key.length == 1 && event.key.charCodeAt(0) >= "0".charCodeAt(0) &&
                    event.key.charCodeAt(0) <= "9".charCodeAt(0))) {
                // If not 0 ~ 9 pressed
                event.preventDefault();
            }
        }
    }
}
</script>

<style scoped>
input {
    font-size: 1.5em;
}
</style>