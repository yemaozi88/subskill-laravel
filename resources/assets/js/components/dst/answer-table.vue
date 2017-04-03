<template>

<div>
    <p>
        正解： {{ correctAnswerText }} <br>
        あなたの答え： {{ userAnswerText }}
        (<strong :class="isAnswerCorrectTextClass">{{ isAnswerCorrectText }}</strong>) <br>
    </p>
</div>

</template>

<script>
import _ from "lodash"

export default {
    props: {
        answer: Object
    },
    computed: {
        userAnswerText() {
            return _.join(this.answer.userAnswer, ", ");
        },
        correctAnswerText() {
            return _.join(this.answer.correctAnswer, ", ");
        },
        isAnswerCorrectText() {
            return this.answer.isAnswerCorrect ? "正" : "誤";
        },
        isAnswerCorrectTextClass() {
            return this.answer.isAnswerCorrect ? "right" : "wrong";
        }
    }
}
</script>

<style scoped>
strong.right {
    color: green;
}
strong.wrong {
    color: red;
}
</style>