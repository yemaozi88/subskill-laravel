<template>

<div>
    <button class="btn btn-default" :disabled="isShowQuestionBtnDisabled" 
            v-on:click="onShowQuestionBtnClicked">問題を見る</button>
    <ul class="list-group" v-if="isShowQuestionList">
        <li class="list-group-item text-primary text-bigger">
            <span class="glyphicon glyphicon-time"></span> 
            <strong>{{ remainedTime }}</strong>s
        </li>
        <li v-for="text in sentences" class="list-group-item">{{ text }}</li>
    </ul>
    <button class="btn btn-default" v-if="isShowQuestionList" v-on:click="onGoNextBtnClicked">次へ進む</button>
</div>

</template>



<script>

export default {
    data () {
        return {
            state: 1,
            elapsedTime: 0,
            timer: null
        }
    },
    props: {
        /**
         * Example:
         * ["sentence1", "sentence2"]
         */
        sentences: Array,
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
            this.$emit("question-displayed");
        }
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