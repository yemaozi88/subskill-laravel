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
                    「問題を再生」ボタンを押すと、数文の英文が連続して流れます。<br>
                    英文は一度しか流れず、聞きなおすことはできません。
                </p>
                <p>
                    再生が終了すると、解答欄が表示されます。<br>
                    それぞれの文章の最後の単語、および内容の正誤を答えてください。<br>
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

            <question-part-one v-if="ifShowQuestionDisplayer" v-on:ended="onQuestionPartOneEnded"
                    :wav-urls="currentQuestionWavUrls">
            </question-part-one>

            <answer-part-two v-if="isShowAnswerPartTwo" :questions="currentQuestions" @changed="onAnswerPartTwoChanged">
            </answer-part-two>
            <button class="btn btn-default" 
                    v-on:click="onAnswerPartTwoBtnClicked" v-if="isShowAnswerPartTwo" :disabled="isSendingData">
                    次へ進む
            </button>

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
import QuestionPartOne from "./question-part-one"
import AnswerPartTwo from "./answer-part-two"
import AnswerTable from "../share/answer-table"
import LoadingIcon from "../share/loading-icon"
import $ from "jquery"
import _ from "lodash"
require("../../helpers")

export default {
    components: {
        "quiz-index-header": QuizIndexHeader,
        "answer-table": AnswerTable,
        "loading-icon": LoadingIcon,
        'question-part-one': QuestionPartOne,
        "answer-part-two": AnswerPartTwo
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
        isShowAnswerPartTwo () {
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
        currentQuestionWavUrls() {
            return _.map(this.rawData.questionSets[this.setIndex].wavs, url => this.audioUrlPrefix + "/" + url);
        },
        currentAnswers() {
            return this.answers[this.setIndex];
        },
        audioUrlPrefix () {
            return this.manifestUrl.substr(0, this.manifestUrl.lastIndexOf("/"));
        },
        isCurrentAnswersLegal () {
            if (!(this.answers[this.setIndex] instanceof Array)) {
                return false;
            }
            if (this.answers[this.setIndex].length != this.currentSetQuestionNum) {
                return false;
            }
            for (var answer of this.answers[this.setIndex]) {
                if (answer === null || answer === undefined) {
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
        onQuestionPartOneEnded (answers) {
            while (this.answers.length <= this.setIndex) {
                this.answers.push(null);
            }
            this.answers[this.setIndex] = [];
            for (var i = 0; i < answers.length; i++) {
                this.answers[this.setIndex].push({
                    judgement: answers[i] == 1,
                    isJudgementCorrect: (answers[i] == 1) == this.currentQuestions[i].correctness
                });
            }
            this.state = 3;
        },
        onAnswerPartTwoBtnClicked () {
            if (!this.isCurrentAnswersLegal) {
                alert("\n単語に英文字以外の文字は入れないでくさい。");
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
        onAnswerPartTwoChanged(data) {
            var newAnswers =  _.clone(this.answers[this.setIndex]);
            for (var i = 0; i < data.length; i++) {
                newAnswers[i] = _.assign({}, newAnswers[i], {
                    word: data[i],
                    isWordCorrect: data[i] == this.currentQuestions[i].lastWord
                });
            }
            this.answers.splice(this.setIndex, 1, newAnswers);
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