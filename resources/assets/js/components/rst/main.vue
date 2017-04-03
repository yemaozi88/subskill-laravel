<template>
<div>
    <div class="row">
        <div class="col-md-12">
            <div v-if="ifShowManual">
                <div class="page-header">
                    <h1>回答方法の説明</h1>
                </div>
                <p>
                    こんにちは{{ username }}さん。
                </p>
                <p>
                    これはあなたが一度にどれだけの英文を処理できるかを測定するテストです。<br>
                    テストが始まると、数文の英文が一定の時間に表示されます。<br>
                    時間が切れると、解答欄が表示されます。<br>
                    それぞれの文章の最後の単語、および内容の正誤を答えてください。
                </p>
                <p>
                    問題は全部で<strong>{{ questionNum }}</strong>問です。
                </p>
                <button class="btn btn-primary" v-on:click="onStartBtnClicked" :disabled="isStartBtnDisabled">
                    始める
                </button>
            </div>

            <quiz-index-header :index="setIndex + 1" :question-num="currentSetQuestionNum" v-if="ifShowQuizIndexHeader">
            </quiz-index-header>

            <question-displayer :sentences="currentQuestionSentences" v-if="ifShowQuestionDisplayer" 
                    v-on:question-displayed="onQuestionDisplayed" :time-limit="currentQuestionTimeLimit">
            </question-displayer>

            <answer-sheet-list :questions="currentQuestions" v-if="isShowAnswerSheetList"
                    v-on:answer-changed="onAnswerChanged"></answer-sheet-list>
            <button class="btn btn-default" 
                    v-on:click="onAnswerSheetBtnClicked" v-if="isShowAnswerSheetList" :disabled="isSendingData">
                    次へ進む</button>

            <answer-table v-if="isShowAnswerTable" :user-answers="currentAnswers"
                    :correct-answers="currentQuestions"></answer-table>
            <button class="btn btn-default"
                    v-on:click="onAnswerTableBtnClicked" v-if="isShowAnswerTable" 
                    :disabled="isSendingData">次へ進む</button>
            <loading-icon v-if="isShowLoadingIcon"></loading-icon>

            <div v-if="isShowSuccessMessage">
                <p class="bg-success">
                データの送信に成功しました。
                </p>
            </div>
        </div>
            
    </div>
</div>
</template>

<script>    
import QuizIndexHeader from "../share/quiz-index-header"
import AnswerSheetList from "../share/answer-sheet-list"
import QuestionDisplayer from "./question-displayer"
import AnswerTable from "../share/answer-table"
import LoadingIcon from "../share/loading-icon"
import $ from "jquery"
require("../../helpers")

export default {
    components: {
        "quiz-index-header": QuizIndexHeader,
        "answer-sheet-list": AnswerSheetList,
        "question-displayer": QuestionDisplayer,
        "answer-table": AnswerTable,
        "loading-icon": LoadingIcon
    },
    data: function() {
        return {
            state: 1,
            setIndex: 0,
            questionLoaded: false,
            /**
             * See manifest.json
             */
            rawData: {},
            answers: [],
            isSendingData: false
        };
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
        isShowAnswerSheetList () {
            return this.state == 3;
        },
        isShowAnswerTable () {
            return this.state == 4;
        },
        isShowLoadingIcon () {
            return this.isSendingData;
        },
        isShowSuccessMessage() {
            return this.state == 5;
        },
        isStartBtnDisabled () {
            return !this.questionLoaded;
        },
        allQuestions () {
            var ret = [];
            var count = 0;
            for (var questions of this.rawData.questionSets) {
                var ta = [];
                for (var answer of questions.answers) {
                    ta.push({
                        correctness: answer.correctness,
                        lastWord: answer.lastWord,
                        id: count + 1
                    });
                    count++;
                }
                ret.push(ta);
            }
            return ret;
        },
        currentQuestions () {
            return this.allQuestions[this.setIndex];
        },
        currentSetQuestionNum() {
            return this.currentQuestions.length;
        },
        currentQuestionSentences() {
            return this.rawData.questionSets[this.setIndex].texts;
        },
        currentQuestionTimeLimit() {
            return this.rawData.questionSets[this.setIndex].timeLimit;
        },
        currentAnswers() {
            return this.answers[this.setIndex];
        },
        isCurrentAnswersLegal () {
            if (!(this.answers[this.setIndex] instanceof Array)) {
                return false;
            }
            if (this.answers[this.setIndex].length != this.currentSetQuestionNum) {
                return false;
            }
            for (var answer of this.answers[this.setIndex]) {
                if (answer === null) {
                    return false;
                }
                if (!answer.hasOwnProperty("word") || (typeof answer.word) !== "string") {
                    return false;
                }
                if (!answer.hasOwnProperty("judgement") || (typeof answer.judgement) !== "boolean") {
                    return false;
                }
                if (!Helpers.validateEnglishWord(answer.word)) {
                    return false;
                }
            }
            return true;
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
            if (!this.isCurrentAnswersLegal) {
                alert("文の正誤を選択してください。\n単語に英文字以外の文字は入れないでくさい。");
                return;
            }
            if (this.showAnswer) {
                this.state = 4;
            } else {
                this.goNextQuestionSet();
            }
        },
        onAnswerTableBtnClicked () {
            this.goNextQuestionSet();
        },
        onAnswerChanged(data) {
            while (this.answers.length <= this.setIndex) {
                this.answers.push(null);
            }
            this.answers.splice(this.setIndex, 1, data);
        },
        goNextQuestionSet () {
            if (this.setIndex < this.allQuestions.length - 1) {
                // If not the last set, go to next set
                this.setIndex++;
                this.state = 2;
            } else {
                // otherwise send answer
                this.sendAnswer();
            }
        },
        sendAnswer () {
            var sentData = {
                correct_num: 0,
                question_num: 0,
                username: this.username,
                group_name: this.groupName,
                quiz_set_name: this.quizSetName,
                last_word_list: '',
                judgement_list: ''
            };

            for (var setAnswers of this.answers) {
                for (var content of setAnswers) {
                    sentData.question_num++;
                    if (content.isJudgementCorrect && content.isWordCorrect) {
                        sentData.correct_num++;
                    }
                    sentData.last_word_list += "#" + content.word;
                    sentData.judgement_list += "#" + (content.judgement ? 'T' : 'F');
                }
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
        }
    },
    mounted () {
        var jqxhr = $.ajax({url: this.manifestUrl, method: "GET", dataType: "json"});
        jqxhr.done((response)=>{
            this.rawData = response;
            this.questionLoaded = true;
        }).fail((jqXHR, textStatus, errorThrown)=>{
            alert("Some error happened:" + errorThrown.toString());
        });
    }
}

</script>

<style>
</style>