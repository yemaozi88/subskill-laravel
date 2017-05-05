<template>

<div><div class="row"><div class="col-md-12">
    <div v-if="ifShowManual">
        <div class="page-header">
            <h1>回答方法の説明</h1>
        </div>
        <p>
            こんにちは{{ username }}さん。
        </p>
        <p>
            これはあなたが一度にどれだけの英文数字を記憶できるかを測定するテストです。<br>
            テストが始まると、英文数字の音声が連続再生されます。<br>
            再生が終わると、解答欄が表示されます。<br>
            解答欄の指示に応じて、順に、または逆順に再生された数字を入力してください。<br>
            数字と数字の間は、スペースやカンマなどを置かずに、続けて入力してください。<br>
            入力が終わったら、次へボタンを押してください。
        </p>
        <p>
            問題は全部で<strong>{{ questionNum }}</strong>問です。
        </p>
        <button class="btn btn-primary" v-on:click="onStartBtnClicked" :disabled="isStartBtnDisabled">
            始める
        </button>
    </div>
    <quiz-index-header :index="setIndex + 1" :question-num="currentQuestionNum" v-if="ifShowQuizIndexHeader">
    </quiz-index-header>
    <question-displayer :question="currentQuestion" v-if="ifShowQuestionDisplayer" 
            v-on:question-displayed="onQuestionDisplayed">
    </question-displayer>
    <answer-sheet :question="currentQuestion" v-if="isShowAnswerSheet"
            v-on:answer-changed="onAnswerChanged"></answer-sheet>
    <button class="btn btn-default" v-on:click="onAnswerSheetBtnClicked" v-if="isShowAnswerSheet"
            :disabled="isAnswerSheetBtnDisabled">次へ進む</button>
    
    <answer-table :answer="currentAnswerForTable" v-if="isShowAnswerTable"></answer-table>
    <button class="btn btn-default" v-on:click="onAnswerTableBtnClicked" v-if="isShowAnswerTable"
            :disabled="isSendingData">次へ進む</button>
    <loading-icon v-if="isShowLoadingIcon"></loading-icon>

    <div v-if="isShowSuccessMessage">
        <p class="bg-success">
        データの送信に成功しました。
        </p>
    </div>

</div></div></div>

</template>

<script>
import QuizIndexHeader from "../share/quiz-index-header"
import QuestionDisplayer from "./question-displayer"
import AnswerSheet from "./answer-sheet"
import AnswerTable from "./answer-table"
import LoadingIcon from "../share/loading-icon"
import _ from "lodash"
import $ from "jquery"
require("../../helpers.js")

export default {
    components: {
        "quiz-index-header": QuizIndexHeader,
        "question-displayer": QuestionDisplayer,
        "answer-sheet": AnswerSheet,
        "answer-table": AnswerTable,
        "loading-icon": LoadingIcon
    },
    data () {
        return {
            state: 1,
            setIndex: 0,
            questionLoaded: false,
            rawData: null,
            questions: null,
            answers: [],
            isSendingData: false
        }
    },
    props: {
        username: {
            default: "Default_Username"
        },
        groupName: {
            default: "Default_Group_Name"
        },
        quizSetName: {
            type: String,
            default: "default_quiz_set"
        },
        questionNum: {
            type: Number,
            required: true
        },
        manifestUrl: {
            type: String,
            required: true
        },
        showAnswer: {
            type: Boolean,
            default: false
        },
        sendAnswerUrl: {
            type: String,
            require: true
        }
    },
    computed: {
        ifShowManual () {
            return this.state == 1;
        },
        ifShowQuizIndexHeader () {
            return this.state == 2 || this.state == 3 || this.state == 4;
        },
        ifShowQuestionDisplayer () {
            return this.state == 2;
        },
        isShowAnswerSheet () {
            return this.state == 3;
        },
        isShowAnswerTable () {
            return this.state == 4;
        },
        isShowLoadingIcon() {
            return this.isSendingData;
        },
        isShowSuccessMessage () {
            return this.state == 5;
        },
        isStartBtnDisabled () {
            return !this.questionLoaded;
        },
        isAnswerSheetBtnDisabled () {
            return !this.isCurrentAnswerLegal || this.isSendingData;
        },
        allQuestionConfigs () {
            return this.rawData.questionSets[this.quizSetName];
        },
        currentQuestionConfig () {
            return this.allQuestionConfigs[this.setIndex];
        },
        currentQuestion () {
            return this.questions[this.setIndex];
        },
        currentQuestionNum () {
            return this.currentQuestion.num;
        },
        audioUrlPrefix () {
            return this.manifestUrl.substr(0, this.manifestUrl.lastIndexOf("/"));
        },
        isCurrentAnswerLegal() {
            if (this.answers.length <= this.setIndex) {
                return false;
            }
            var answer = this.answers[this.setIndex];
            if (answer.userAnswer.length != this.currentQuestion.num) {
                return false;
            }
            for (var i = 0; i < answer.userAnswer.length; i++) {
                if (!Number.isInteger(answer.userAnswer[i])) {
                    return false;
                }
            }
            return true;
        },
        currentAnswerForTable() {
            return {
                userAnswer: this.answers[this.setIndex].userAnswer,
                correctAnswer: this.currentQuestion.order == "F" ?
                    this.currentQuestion.sequence : _.clone(this.currentQuestion.sequence).reverse(),
                isAnswerCorrect: this.answers[this.setIndex].isAnswerCorrect
            };
        }
    },
    methods: {
        onStartBtnClicked () {
            this.state = 2;
        },
        onQuestionDisplayed () {
            this.state = 3;
        },
        onAnswerSheetBtnClicked () {
            if (this.showAnswer) {
                this.state = 4;
            } else {
                this.goNextQuestionSet();
            }
        },
        onAnswerTableBtnClicked() {
            this.goNextQuestionSet();
        },
        onAnswerChanged (data) {
            while (this.answers.length <= this.setIndex) {
                this.answers.push(null);
            }
            this.answers.splice(this.setIndex, 1, data);
        },
        goNextQuestionSet () {
            if (this.setIndex < this.questions.length - 1) {
                // If not the last set, go to next set
                this.setIndex++;
                this.state = 2;
            } else {
                // otherwise send answer
                this.sendAnswer();
            }
        },
        sendAnswer() {
            var sentData = {
                correct_num: _.countBy(this.answers, answer => answer.isAnswerCorrect ? '1' : '0')['1'] || 0,
                question_num: this.questions.length,
                username: this.username,
                group_name: this.groupName,
                quiz_set_name: this.quizSetName,
                user_digit_list: "",
                correct_digit_list: "",
                order_list: ""
            };

            for (var i = 0; i < this.questions.length; i++) {
                sentData.user_digit_list += "#" + _.join(this.answers[i].userAnswer, ",");
                var correctAnswer = _.clone(this.questions[i].sequence);
                if (this.questions[i].order != "F") {
                    correctAnswer.reverse();
                }
                sentData.correct_digit_list += '#' + _.join(correctAnswer, ",");
                sentData.order_list += "#" + this.questions[i].order;
            }

            var jqxhr = $.ajax({url: this.sendAnswerUrl, method: "POST", data: sentData, dataType: "json"});
            jqxhr.done((response)=>{
                if (response.ret == "ok") {
                    // Go to the final message
                    this.state = 5;
                } else {
                    alert("Some error happened:" + response.reason);
                }
                this.isSendingData = false;
            }).fail((jqXHR, textStatus, errorThrown)=>{
                alert("Some error happened:" + errorThrown.toString());
                this.isSendingData = false;
            });

            this.isSendingData = true;
        },
        generateQuestions () {
            this.questions = [];
            var configs = this.rawData.questionSets[this.quizSetName];
            var qNum = configs.length;
            for (var i = 0; i < qNum; i++) {
                var s = {num: configs[i].num, order: configs[i].order, sequence: [], urls: []};
                for (var j = 0; j < configs[i].num; j++) {
                    var t = Helpers.randInt(0, 9);
                    s.sequence.push(t);
                    s.urls.push(this.audioUrlPrefix + "/" + this.rawData.audios[t.toString()]);
                }
                this.questions.push(s);
            }
        }
    },
    mounted () {
        var jqxhr = $.ajax({url: this.manifestUrl, method: "GET", dataType: "json"});
        jqxhr.done((response)=>{
            this.rawData = response;
            this.questionLoaded = true;
            this.generateQuestions();
        }).fail((jqXHR, textStatus, errorThrown)=>{
            alert("Some error happened:" + errorThrown.toString());
        });
    }
}
</script>