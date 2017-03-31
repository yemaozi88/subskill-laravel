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
            <quiz-index-header index="3" question-num="2" v-if="ifShowQuizIndexHeader"></quiz-index-header>
            <question-displayer :sentences="sentences" v-if="ifShowQuestionDisplayer" 
                    v-on:question-displayed="onQuestionDisplayed"></question-displayer>
            <answer-sheet-list :questions="currentQuestions" v-if="isShowAnswerSheetList"></answer-sheet-list>
        </div>
            
    </div>
</div>
</template>

<script>    
import QuizIndexHeader from "../share/quiz-index-header"
import AnswerSheetList from "../share/answer-sheet-list"
import QuestionDisplayer from "./question-displayer.vue"
import $ from "jquery"

export default {
    components: {
        "quiz-index-header": QuizIndexHeader,
        "answer-sheet-list": AnswerSheetList,
        "question-displayer": QuestionDisplayer
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
            sentences: ["nihaoa", "wobuhao"]
        };
    },
    props: {
        username: {
            default: "Default_Username"
        },
        groupName: {
            default: "Default_Group_Name"
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
        }
    },
    computed: {
        ifShowManual () {
            return this.state == 1;
        },
        ifShowQuizIndexHeader () {
            return this.state == 2 || this.state == 3;
        },
        ifShowQuestionDisplayer () {
            return this.state == 2;
        },
        isShowAnswerSheetList () {
            return this.state == 3;
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
        }
    },
    methods: {
        onStartBtnClicked () {
            this.state = 2;
        },
        onQuestionDisplayed () {
            this.state = 3;
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