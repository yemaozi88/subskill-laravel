(function () {

    var getUniqueStr = function(myStrong) {
        var strong = 1000;
        if (myStrong) strong = myStrong;
        return new Date().getTime().toString(16)  + Math.floor(strong*Math.random()).toString(16)
    }

    Vue.component('quiz-index-header', {
        template: '<div class="page-header">\
        <h1>第{{ index }}問</h1>\
        </div>',
        props: [
            'index'
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

    var app = new Vue({
        el: '#app',
        data: {
            step: 0,
            quizIndex: 1,
            audioWavSrcs: [
                '/upload/wm/set2/1.wav',
                '/upload/wm/set2/2.wav'
            ],
            quizContents: [
                {
                    quizIndex: 1,
                    firstChar: 'y'
                },
                {
                    quizIndex: 2,
                    firstChar: 'p'
                }
            ]
        },
        computed: {
            showIntro: function () {
                return this.step == 0;
            },
            showQuiz: function () {
                return this.step >= 1;
            },
            showQuestion: function () {
                return this.step >= 2;
            }
        },
        methods: {
            introBtnClicked: function () {
                this.step = 1;
            },
            audioPlayFinished: function () {
                this.step = 2;
            },
            submit: function () {
                alert('Not Implemented');
            }
        }
    });

}());



