<template>
<div>
    <answer-sheet v-for="(quest, index) in questions" :index="index" :word="quest.lastWord" :key="quest.id"
            v-on:answer-changed="onAnswerChanged">
    </answer-sheet>
</div>
</template>

<script>
import AnswerSheet from "./answer-sheet"

export default {
    components: {
        "answer-sheet": AnswerSheet
    },
    data () {
        return {
            /**
             * Example:
             * [ {word: "word", judgement: true, id: 1, isWordCorrect: true, isJudgementCorrect: true} ]
             */
            answers: []
        }
    },
    props: {
        /**
         * Example:
         * [ {lastWord: "word", correctness: true, id: 1} ]
         */
        questions: Array
    },
    methods: {
        onAnswerChanged(index, data) {
            while (this.answers.length <= index) {
                this.answers.push(null);
            }
            this.answers.splice(index, 1, {
                word: data.word,
                judgement: data.judgement,
                isWordCorrect: data.word === this.questions[index].lastWord,
                isJudgementCorrect: data.judgement === this.questions[index].correctness
            });
            this.$emit("answer-changed", this.answers);
        }
    }
}

</script>