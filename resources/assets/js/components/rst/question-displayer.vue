<template>

<div>
    <button class="btn btn-default" :disabled="isShowQuestionBtnDisabled" 
            v-on:click="onShowQuestionBtnClicked">問題を見る</button>
    <ul class="list-group" v-if="isShowQuestionList">
        <li class="list-group-item text-primary text-bigger">
            <span class="glyphicon glyphicon-time"></span> 
            <strong>{{ remainedTime }}</strong>s
        </li>
        <li v-for="(text, index) in sentences" class="list-group-item">
            {{ text }}
            <radio-box :items="['正しい', '誤り']" v-model="answers[index]">
                文の正誤：
            </radio-box>
        </li>
    </ul>
    <button class="btn btn-default" v-if="isShowQuestionList" v-on:click="onGoNextBtnClicked">次へ進む</button>
</div>

</template>



<script>
import RadioBox from "../share/radio-box"
export default {
    components: {
        "radio-box": RadioBox
    },
    data () {
        return {
            state: 1,
            elapsedTime: 0,
            timer: null,
            /** [value0, value1, ...] */
            answers: []
        }
    },
    props: {
        /**
         * Example:
         * ["sentence1", "sentence2"]
         */
        sentences: {
            type: Array,
            default: function() {
                return [];
            }
        },
        /* In seconds. */
        timeLimit: {
            type: Number,
            default: 20
        },
        /* In milliseconds. */
        timeRefreshInterval: {
            type: Number,
            default: 100
        }
    },
    computed: {
        isShowQuestionBtnDisabled () {
            return this.state != 1;
        },
        isShowQuestionList () {
            return this.state == 2;
        },
        remainedTime () {
            return Math.round((this.timeLimit - this.elapsedTime) * 100)/100;
        }
    },
    methods: {
        onShowQuestionBtnClicked () {
            this.state = 2;
            this.elapsedTime = 0;
            this.timer = setInterval(() => {
                this.elapsedTime += this.timeRefreshInterval / 1000;
                if (this.elapsedTime >= this.timeLimit) {
                    this.timerStop();
                }
            }, this.timeRefreshInterval);
        },
        onGoNextBtnClicked () {
            this.timerStop();
        },
        timerStop () {
            this.elapsedTime = this.timeLimit;
            this.state = 1;
            clearInterval(this.timer);
            this.timer = null;
            this.$emit("question-displayed", this.answers);
        },
        reallocateAnswers() {
            this.answers = [];
            while (this.answers.length < this.sentences.length) {
                this.answers.push(1);
            }
        }
    },
    watch: {
        sentences(newSentences) {
            this.reallocateAnswers();
        }
    },
    mounted() {
        this.reallocateAnswers();
    }
}

</script>

<style scoped>
ul {
    margin-top: 10px;
}
.text-bigger {
    font-size: 1.5em;
}
</style>