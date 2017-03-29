(function () {

    var getUniqueStr = function(myStrong) {
        var strong = 1000;
        if (myStrong) strong = myStrong;
        return new Date().getTime().toString(16)  + Math.floor(strong*Math.random()).toString(16)
    };

    Vue.component('quiz-index-header', {
        template: '<div class="page-header">\
        <h1>第{{ index }}問<small> 計{{ questionNum }}文連続</small></h1>\
        </div>',
        props: [
            'index',
            'questionNum'
        ]
    });

    Vue.component('quiz-player', {
        template: '<div>\
        <button class="btn btn-default" :disabled="!enabled" v-on:click="playAudio">問題を再生</button>\
        <audio preload="auto" v-for="(src, index) in audioWavSrcs" v-on:canplay="onCanPlay"\
            v-on:ended="onEnded" ref="audios">\
            <source :src="src" type="audio/wav" >\
            Your browser does not support the audio element.\
        </audio>\
        </div>'
        ,
        data: function () {
            return {
                audioLoadedNum: 0,
                currentPlayingIndex: -1,
                played: false
            }
        },
        props: {
            audioWavSrcs: {
                default: []
            },
            onlyOnce: {
                type: Boolean,
                default: false
            }
        },
        computed: {
            isPlaying: function () {
                return this.currentPlayingIndex != -1;
            },
            enabled: function () {
                // TODO(sonicmisora): this.audioLoadedNum would be added too many times because
                // onCanPlay would be called after onEnded
                return !this.isPlaying && this.audioLoadedNum >= this.audioTotalNum &&
                    (!this.onlyOnce || !this.played);
            },
            audioTotalNum: function () {
                return this.audioWavSrcs.length;
            }
        },
        methods: {
            onCanPlay: function () {
                this.audioLoadedNum++;
            },
            onEnded: function () {
                if (this.currentPlayingIndex == this.audioTotalNum - 1) {
                    // If this is the last one
                    this.currentPlayingIndex = -1;
                    this.played = true;
                    this.$emit('play-finished');
                    return;
                }
                this.currentPlayingIndex++;
                this.$refs.audios[this.currentPlayingIndex].play();
            },
            playAudio: function () {
                this.currentPlayingIndex = 0;
                if (this.$refs.audios.length == 0) {
                    return;
                }
                this.$refs.audios[0].play();
            },
            reset: function () {
                this.audioLoadedNum = 0;
                this.currentPlayingIndex = -1;
                this.played = false;
            }
        }
    });

    Vue.component('wm-question', {
        template: '\
        <div>\
            <h3 class="subtitle">第{{ index }}文</h3>\
            <div class="form-group form-inline">\
                <label :id="labelId" class="control-label">(1)文章の内容:</label>\
                <div class="radio">\
                    <label>\
                        <input type="radio" :name="radioName" value="1" v-model="answer1">\
                        正しい\
                    </label>\
                </div>\
                <div class="radio">\
                    <label>\
                        <input type="radio" :name="radioName" value="2" v-model="answer1">\
                        誤り\
                    </label>\
                </div>\
            </div>\
            <div class="form-group form-inline">\
                <label :id="labelId" class="control-label">(2)最後の単語:</label>\
                {{ wordPrefix }}<input :id="inputId" type="text" class="" v-model="answer2">\
            </div>\
        </div>',
        props: {
            index: null,
            wordPrefix: {
                type: String
            },
            idPrefix: {
                type: String,
                default: function () {
                    return getUniqueStr();
                }
            }
        },
        data: function () {
            return {
                answer1: "",
                answer2: ""
            }
        },
        computed: {
            labelId: function () {
                return this.idPrefix + '_label';
            },
            inputId: function () {
                return this.idPrefix + '_input';
            },
            radioName: function () {
                return this.idPrefix + '_radio';
            }
        }
    });

    var configElement = $('#config');
    var manifestUrl = configElement.data('manifest-url');
    var audioFolderUrl = configElement.data('audio-folder-url');
    var sendAnswerUrl = configElement.data('send-answer-url');
    var username = configElement.data('username');
    var groupName = configElement.data('group-name');
    var quizSetName = configElement.data('quiz-set-name');
    var showAnswerMode = configElement.data('show-answer') == 1;

    var validateAnswer = function () {
        for (var wmq of this.$refs.questions) {
            if (wmq.answer1 != 1 && wmq.answer1 != 2) {
                return false;
            }
            for (var char of wmq.answer2) {
                var charCode = char.charCodeAt(0);
                if (!(charCode >= 'a'.charCodeAt(0) && charCode <= 'z'.charCodeAt(0) ||
                    charCode >= 'A'.charCodeAt(0) && charCode <= 'Z'.charCodeAt(0) ||
                    char === '-')) {
                    return false;
                }
            }
        }
        return true;
    };

    var collectAnswer = function () {
        var ansList = [];
        var count = 0;
        for (var wmq of this.$refs.questions) {
            var judg = wmq.answer1 == 1;
            var lastWord = wmq.wordPrefix + wmq.answer2;
            var correctAns = this.rawData.questionSets[this.setIndex].answers[count];
            var t_ans = {
                judgement: judg,
                isJudgementCorrect: correctAns.correctness == judg,
                lastWord: lastWord,
                isLastWordCorrect: correctAns.lastWord == lastWord
            };
            ansList.push(t_ans);
            count++;
        }
        return ansList;
    };

    var sendAnswer = function () {
        var sentData = {
            correct_num: 0,
            question_num: 0,
            username: username,
            group_name: groupName,
            quiz_set_name: quizSetName,
            last_word_list: '',
            judgement_list: ''
        };
        for (var set_answers of this.answers) {
            for (var content of set_answers) {
                sentData.question_num++;
                if (content.isJudgementCorrect && content.isLastWordCorrect) {
                    sentData.correct_num++;
                }
                sentData.last_word_list += "#" + content.lastWord;
                sentData.judgement_list += "#" + (content.judgement ? 'T' : 'F');
            }
        }
        var jqxhr = $.ajax({url: sendAnswerUrl, data: sentData, method: "POST"});
        jqxhr.done((response)=>{
            this.waiting = false;
            this.step = 3;
        }).fail((error)=>{
            alert("Some error happened:" + error.toString());
            this.waiting = false;
            this.step = 3;
        });
    };

    var switchToNextSet = function () {
        this.setIndex++;
        this.step = 1;
        //this.$refs.quizPlayer.reset();
        this.loadSet(this.rawData.questionSets[this.setIndex]);
    };

    var app = new Vue({
        el: '#app',
        data: {
            isDataLoaded: false,
            showAnswerMode: showAnswerMode,
            audioFolderUrl: "",
            rawData: null,
            waiting: false,
            step: 0,
            setIndex: 0, // Used for recording how many sets are done in current session
            audioWavSrcs: [],
            quizContents: [
                {
                    quizIndex: 1,
                    firstChar: 'y'
                }
            ],
            answers: [
                [
                    {
                        judgement: true,
                        isJudgementCorrect: true,
                        lastWord: "year",
                        isLastWordCorrect: true
                    },
                    {
                        judgement: true,
                        isJudgementCorrect: true,
                        lastWord: "cook",
                        isLastWordCorrect: false
                    }
                ]
            ]
        },
        computed: {
            showIntro: function () {
                return this.step == 0 && !this.waiting;
            },
            showQuiz: function () {
                return (this.step == 1 || this.step == 2) && !this.waiting;
            },
            showQuestion: function () {
                return (this.step == 2) && !this.waiting;
            },
            showFinishedMessage: function () {
                return (this.step == 3) && !this.waiting;
            },
            showAnswer: function () {
                return this.step == 4 && !this.waiting;
            },
            showWaiting: function () {
                return this.waiting;
            },
            allWavs: function () {
                var ret = [];
                for (var questionSet of this.rawData.questionSets) {
                    var newWavs = [];
                    for (var wav of questionSet.wavs) {
                        newWavs.push(audioFolderUrl + "/" + wav);
                    }
                    ret.push(newWavs);
                }
                return ret;
            },
            questionNum: function () {
                return this.quizContents.length;
            },
            submitBtnText: function () {
                if (this.setIndex >= this.rawData.questionSets.length - 1) {
                    return "回答を送信";
                } else {
                    return "次へ";
                }
            }
        },
        methods: {
            introBtnClicked: function () {
                this.step = 1;
            },
            audioPlayFinished: function () {
                this.step = 2;
            },
            loadSet: function (questionSet) {
                var newWavs = [];
                for (var wav of questionSet.wavs) {
                    newWavs.push(audioFolderUrl + "/" + wav);
                }
                this.audioWavSrcs = newWavs;
                var newQuizContents = [];
                for (var ans of questionSet.answers) {
                    newQuizContents.push({quizIndex: 1, firstChar: ans.lastWord[0]});
                }
                this.quizContents = newQuizContents;
            },
            dataLoaded: function (data, audioFolderUrl) {
                this.isDataLoaded = true;
                this.answers = [];
                this.rawData = data;
                this.setIndex = 0;
                this.audioFolderUrl = audioFolderUrl;
                //alert(data);
                var questionSet = data.questionSets[this.setIndex];
                this.loadSet(questionSet);
            },
            submit: function () {
                if (this.step == 2) {
                    // If now trying to submit the answer
                    if (!validateAnswer.call(this)) {
                        // Do nothing if answer is not correctly filled
                        alert("文の正誤を選択してください。\n単語に英文字以外の文字は入れないでくさい。");
                        return;
                    }
                    // add current answers to the answer container
                    var ansList = collectAnswer.call(this);
                    this.answers.push(ansList);
                    if (this.showAnswerMode) {
                        // We need to show the answers first
                        this.step = 4;
                    } else {
                        // Otherwise just submit answer or go next
                        if (this.setIndex >= this.rawData.questionSets.length - 1) {
                            // If we have finished all the tests
                            this.waiting = true;
                            sendAnswer.call(this);
                        } else {
                            switchToNextSet.call(this);
                        }
                    }
                } else if (this.step == 4) {
                    // If now showing the answer for last question set
                    if (this.setIndex >= this.rawData.questionSets.length - 1) {
                        // If we have finished all the tests
                        this.waiting = true;
                        sendAnswer.call(this);
                    } else {
                        switchToNextSet.call(this);
                    }
                }
            }
        }
    });

    function dataFetchFail(error) {
        //alert("Failed fetching score data:" + error.toString());
        throw error;
    }

    function dataFetchDone(dataCollection) {
        var data = dataCollection[0];
        app.dataLoaded(data, audioFolderUrl);
    }

    Promise.all([
        $.ajax({url: manifestUrl, dataType: 'json'})
    ]).then(dataFetchDone)
        .catch(dataFetchFail);

}());



